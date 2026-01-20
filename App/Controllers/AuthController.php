<?php

namespace App\Controllers;

use App\Configuration;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            // Read email from form (input name changed to 'email')
            $email = trim((string)$request->value('email'));
            $password = $request->value('password');

            // Server-side validation: require email and password
            if ($email === '' || $password === null || $password === '') {
                $message = 'E-mail a heslo sú povinné.';
                return $this->html(compact('message'));
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Zadaný e-mail nemá správny formát.';
                return $this->html(compact('message'));
            }

            $logged = $this->app->getAuthenticator()->login($email, $password);
            if ($logged) {
                // Redirect admins to admin dashboard, non-admins to homepage
                $appUser = $this->app->getAuthenticator()->getUser();
                if ($appUser->isAdmin()) {
                    return $this->redirect($this->url("admin.index"));
                }
                return $this->redirect($this->url("home.index"));
            }
        }

        $message = $logged === false ? 'Nesprávny e-mail alebo heslo' : null;
        return $this->html(compact("message"));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        // Terminate the current user session and redirect immediately to the homepage
        $this->app->getAuthenticator()->logout();
        return $this->redirect($this->url('home.index'));
    }

    /**
     * Displays the registration form for new users.
     *
     * This action renders the registration page where users can fill out their details to create a new account.
     *
     * @param Request $request The HTTP request object.
     * @return ViewResponse The response object for rendering the registration page.
     */
    public function newUserRegistration(Request $request): ViewResponse
    {
        return $this->view('Auth/newUserRegistration');
    }

    /**
     * Handles the registration of a new user.
     *
     * This action processes the registration form submission, validates the input,
     * and inserts the new user into the database.
     *
     * @param Request $request The HTTP request object.
     * @return Response The response object for redirecting or rendering a view.
     */
    public function registerUser(Request $request): Response
    {
        // Validate input
        $firstName = $request->value('firstName');
        $lastName = $request->value('lastName');
        $email = $request->value('email');
        $password = $request->value('password');
        $confirmPassword = $request->value('confirmPassword');
        $birthDate = $request->value('birthDate');
        $gender = $request->value('gender');

        if (!$firstName || !$lastName || !$email || !$password || !$confirmPassword || !$birthDate || !$gender) {
            return $this->html(['message' => 'Všetky polia sú povinné.'], 'Auth/newUserRegistration');
        }

        if ($password !== $confirmPassword) {
            return $this->html(['message' => 'Heslo sa nezhoduje. Skúste to znova.'], 'Auth/newUserRegistration');
        }

        // Require password to contain at least 5 letters and at least one digit
        // Count letters using preg_match_all for Unicode letters, fallback to extended Latin range
        $lettersCount = @preg_match_all('/\p{L}/u', $password);
        if ($lettersCount === false) {
            // fallback to extended Latin (covers Slovak diacritics)
            $lettersCount = preg_match_all('/[A-Za-zÀ-ž]/u', $password);
        }
        // Try Unicode digit category first (matches digits from other scripts), fallback to \d
        $hasDigit = @preg_match('/\p{Nd}/u', $password);
        if ($hasDigit === false) {
            $hasDigit = preg_match('/\\d/', $password);
        }
        if ($lettersCount === false || $lettersCount < 5 || !$hasDigit) {
            // provide small, non-sensitive diagnostics to help debugging: number of letters and digit present
            $digitText = $hasDigit ? 'áno' : 'nie';
            $diag = " (písmen: " . ($lettersCount === false ? 'chyba' : $lettersCount) . ", číslo: " . $digitText . ")";
            return $this->html(['message' => 'Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.' . $diag], 'Auth/newUserRegistration');
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->html(['message' => 'Zadaný email nemá správny formát.'], 'Auth/newUserRegistration');
        }

        // Normalize email for comparison
        $emailNormalized = trim(strtolower((string)$email));

        // Check if email is already registered
        $db = $this->app->getDb();
        $stmtCheck = $db->prepare('SELECT COUNT(*) AS cnt FROM Pouzivatelia WHERE LOWER(email) = ?');
        $stmtCheck->execute([$emailNormalized]);
        $countRow = $stmtCheck->fetch();
        if ($countRow && (int)$countRow['cnt'] > 0) {
            return $this->html(['message' => 'Na tento email už existuje registrácia. Zvoľte iný email alebo sa prihláste.'], 'Auth/newUserRegistration');
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        $db = $this->app->getDb();
        $stmt = $db->prepare(
            'INSERT INTO Pouzivatelia (meno, priezvisko, email, heslo, datum_narodenia, pohlavie) VALUES (?, ?, ?, ?, ?, ?)'
        );
        try {
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $birthDate, $gender]);
        } catch (\PDOException $e) {
            // Handle duplicate email race condition (unique constraint) and other DB errors
            $sqlState = $e->getCode();
            if ($sqlState === '23000' || str_contains($e->getMessage(), 'Duplicate')) {
                return $this->html(['message' => 'Tento email už existuje v našej databáze. Zvoľte iný email alebo sa prihláste.'], 'Auth/newUserRegistration');
            }
            // Re-throw unexpected DB errors
            throw $e;
        }

        // Redirect to the success registration page after successful registration
        return $this->redirect($this->url('auth.successRegistration'));
    }

    /**
     * Displays the success registration page.
     *
     * This action renders the success registration view to notify the user of successful registration.
     *
     * @param Request $request The HTTP request object.
     * @return ViewResponse The response object for rendering the success registration page.
     */
    public function successRegistration(Request $request): ViewResponse
    {
        return $this->view('Auth/successRegistration');
    }
}
