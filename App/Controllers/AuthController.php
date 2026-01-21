<?php

namespace App\Controllers;

use App\Configuration;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * AuthController
 *
 * Spravuje prihlasovanie, registráciu a odhlásenie.
 */
class AuthController extends BaseController
{
    /**
     * Presmeruje na prihlasovaciu stránku.
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Spracuje prihlásenie (login) a ochranu proti bruteforce.
     *
     * @return Response
     * @throws Exception
     */
    public function login(Request $request): Response
    {
        $session = $this->app->getSession();

        // nastavenie počtu pokusov a doby zablokovania
        $maxAttempts = 5; // povolené pokusy
        $lockoutSeconds = 3600; // doba zablokovania v sekundách

        // načítať počítadlá zo session
        $attempts = (int)$session->get('login_attempts', 0);
        $lockedUntil = (int)$session->get('login_locked_until', 0);

        // ak je účet zablokovaný, vráti hlášku s časom
        if ($lockedUntil > time()) {
            $remaining = $lockedUntil - time();
            $minutes = (int)ceil($remaining / 60);
            $message = 'Prekročili ste počet povolených pokusov. Skúste znova o ' . date('H:i', $lockedUntil) . ' (približne o ' . $minutes . ' minút).';
            return $this->html(['message' => $message, 'attemptsLeft' => 0, 'lockoutExpiresAt' => $lockedUntil]);
        }

        // ak sa zablokovanie skončilo, vymaže počítadlá
        if ($lockedUntil > 0 && $lockedUntil <= time()) {
            $session->remove('login_attempts');
            $session->remove('login_locked_until');
            $attempts = 0;
            $lockedUntil = 0;
        }

        $logged = null;
        if ($request->hasValue('submit')) {
            // načítať hodnoty z formulára
            $email = trim((string)$request->value('email'));
            $password = $request->value('password');

            // validácia vstupu
            if ($email === '' || $password === null || $password === '') {
                $attemptsLeftRaw = max(0, $maxAttempts - $attempts);
                $displayAttempts = ($attemptsLeftRaw === 1) ? 1 : null;
                $message = 'E-mail a heslo sú povinné.';
                return $this->html(['message' => $message, 'attemptsLeft' => $displayAttempts, 'lockoutExpiresAt' => null]);
            }

            // overenie formátu emailu
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $attemptsLeftRaw = max(0, $maxAttempts - $attempts);
                $displayAttempts = ($attemptsLeftRaw === 1) ? 1 : null;
                $message = 'Zadaný e-mail nemá správny formát.';
                return $this->html(['message' => $message, 'attemptsLeft' => $displayAttempts, 'lockoutExpiresAt' => null]);
            }

            $logged = $this->app->getAuthenticator()->login($email, $password);
            if ($logged) {
                // úspešné prihlásenie -> vymazať počítadlá a presmerovať
                $session->remove('login_attempts');
                $session->remove('login_locked_until');

                $appUser = $this->app->getAuthenticator()->getUser();
                if ($appUser->isAdmin()) {
                    return $this->redirect($this->url("admin.index"));
                }
                return $this->redirect($this->url("home.index"));
            } else {
                // neúspešné prihlásenie -> zvýšiť počet pokusov
                $attempts++;
                $session->set('login_attempts', $attempts);

                if ($attempts >= $maxAttempts) {
                    // nastaviť zablokovanie
                    $lockedUntil = time() + $lockoutSeconds;
                    $session->set('login_locked_until', $lockedUntil);
                    $minutes = (int)ceil($lockoutSeconds / 60);
                    $message = 'Prekročili ste počet povolených pokusov. Na prihlásenie musíte počkať ' . $minutes . ' minút (do ' . date('H:i', $lockedUntil) . ').';
                    return $this->html(['message' => $message, 'attemptsLeft' => 0, 'lockoutExpiresAt' => $lockedUntil]);
                }

                $attemptsLeftRaw = max(0, $maxAttempts - $attempts);
                if ($attemptsLeftRaw === 1) {
                    $message = 'Nesprávny e-mail alebo heslo. Zostáva posledný pokus.';
                    $displayAttempts = 1;
                } else {
                    $message = 'Nesprávny e-mail alebo heslo.';
                    $displayAttempts = null;
                }
                return $this->html(['message' => $message, 'attemptsLeft' => $displayAttempts, 'lockoutExpiresAt' => null]);
            }
        }

        // zobrazenie formulára (GET)
        $attemptsLeftRaw = max(0, $maxAttempts - $attempts);
        $displayAttempts = ($attemptsLeftRaw === 1) ? 1 : null;
        return $this->html(['message' => ($logged === false ? 'Nesprávny e-mail alebo heslo' : null), 'attemptsLeft' => $displayAttempts, 'lockoutExpiresAt' => null]);
    }

    /**
     * Odhlási používateľa a presmeruje na domovskú stránku.
     *
     * @return ViewResponse
     */
    public function logout(Request $request): Response
    {
        // odhlási používateľa
        $this->app->getAuthenticator()->logout();
        return $this->redirect($this->url('home.index'));
    }

    /**
     * Zobrazí registračný formulár.
     *
     * @param Request $request
     * @return ViewResponse
     */
    public function newUserRegistration(Request $request): ViewResponse
    {
        return $this->view('Auth/newUserRegistration');
    }

    /**
     * Spracuje registráciu nového používateľa.
     *
     * @param Request $request
     * @return Response
     */
    public function registerUser(Request $request): Response
    {
        // načíta vstupy z formulára
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

        // overenie zloženia hesla
        $lettersCount = @preg_match_all('/\p{L}/u', $password);
        if ($lettersCount === false) {
            $lettersCount = preg_match_all('/[A-Za-zÀ-ž]/u', $password);
        }
        $hasDigit = @preg_match('/\p{Nd}/u', $password);
        if ($hasDigit === false) {
            $hasDigit = preg_match('/\\d/', $password);
        }
        if ($lettersCount === false || $lettersCount < 5 || !$hasDigit) {
            $digitText = $hasDigit ? 'áno' : 'nie';
            $diag = " (písmen: " . ($lettersCount === false ? 'chyba' : $lettersCount) . ", číslo: " . $digitText . ")";
            return $this->html(['message' => 'Heslo musí obsahovať minimálne 5 písmen a aspoň jedno číslo.' . $diag], 'Auth/newUserRegistration');
        }

        // overenie formátu emailu
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->html(['message' => 'Zadaný email nemá správny formát.'], 'Auth/newUserRegistration');
        }

        // normalizácia emailu
        $emailNormalized = trim(strtolower((string)$email));

        // kontrola, či email už existuje
        $db = $this->app->getDb();
        $stmtCheck = $db->prepare('SELECT COUNT(*) AS cnt FROM Pouzivatelia WHERE LOWER(email) = ?');
        $stmtCheck->execute([$emailNormalized]);
        $countRow = $stmtCheck->fetch();
        if ($countRow && (int)$countRow['cnt'] > 0) {
            return $this->html(['message' => 'Na tento email už existuje registrácia. Zvoľte iný email alebo sa prihláste.'], 'Auth/newUserRegistration');
        }

        // zahashovanie hesla
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // vloženie používateľa do DB
        $db = $this->app->getDb();
        $stmt = $db->prepare(
            'INSERT INTO Pouzivatelia (meno, priezvisko, email, heslo, datum_narodenia, pohlavie) VALUES (?, ?, ?, ?, ?, ?)'
        );
        try {
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $birthDate, $gender]);
        } catch (\PDOException $e) {
            // spracovanie chýb DB (duplicitný email a pod.)
            $sqlState = $e->getCode();
            if ($sqlState === '23000' || str_contains($e->getMessage(), 'Duplicate')) {
                return $this->html(['message' => 'Tento email už existuje v našej databáze. Zvoľte iný email alebo sa prihláste.'], 'Auth/newUserRegistration');
            }
            throw $e;
        }

        // presmerovanie po úspešnej registrácii
        return $this->redirect($this->url('auth.successRegistration'));
    }

    /**
     * Zobrazí stránku o úspešnej registrácii.
     *
     * @param Request $request
     * @return ViewResponse
     */
    public function successRegistration(Request $request): ViewResponse
    {
        return $this->view('Auth/successRegistration');
    }
}

