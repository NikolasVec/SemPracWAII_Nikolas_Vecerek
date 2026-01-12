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
            $logged = $this->app->getAuthenticator()->login($request->value('username'), $request->value('password'));
            if ($logged) {
                return $this->redirect($this->url("admin.index"));
            }
        }

        $message = $logged === false ? 'Bad username or password' : null;
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
        $this->app->getAuthenticator()->logout();
        return $this->html();
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
            return $this->html(['message' => 'All fields are required.'], 'Auth/newUserRegistration');
        }

        if ($password !== $confirmPassword) {
            return $this->html(['message' => 'Heslo sa nezhoduje. Skúste to znova.'], 'Auth/newUserRegistration');
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        $db = $this->app->getDb();
        $stmt = $db->prepare(
            'INSERT INTO Pouzivatelia (meno, priezvisko, email, heslo, datum_narodenia, pohlavie) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $birthDate, $gender]);

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
