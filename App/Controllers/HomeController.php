<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\DB\Connection;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Support\MapPresenter;

/**
 * Trieda HomeController
 * Spracúva akcie pre hlavnú stránku a verejné stránky.
 *
 * Obsahuje endpointy prístupné všetkým používateľom.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Autorizácia akcie (tu povolené všetky akcie).
     *
     * @param string $action Názov akcie
     * @return bool true = povolené
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Zobrazí domovskú stránku.
     */
    public function index(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Zobrazí stránku "informacie".
     */
    public function informacie(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Zobrazí mapu a stanoviská.
     */
    public function mapa(Request $request): Response
    {
        $conn = \Framework\DB\Connection::getInstance();
        $stanoviska = [];
        $error = null;
        try {
            $stmt = $conn->query('SELECT * FROM Stanovisko');
            $stanoviska = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // chybu uložíme pre zobrazenie vo view
            $stanoviska = [];
            $error = $e->getMessage();
        }

        // Použijeme presenter na prípravu dát pre view
        $presenter = new MapPresenter();
        $presented = $presenter->present($stanoviska);

        return $this->html(['stanoviska' => $presented, 'mapa_error' => $error]);
    }
    public function galleryPage(Request $request): Response
    {
        $conn = Connection::getInstance();
        $albums = [];
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'albums'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $stmtA = $conn->query('SELECT * FROM albums ORDER BY created_at DESC');
                $rows = $stmtA->fetchAll();
                foreach ($rows as $row) {
                    $stmtP = $conn->prepare('SELECT * FROM photos WHERE album_id = ? ORDER BY created_at ASC');
                    $stmtP->execute([$row['ID_album']]);
                    $photos = $stmtP->fetchAll();
                    $albums[] = [
                        'album' => $row,
                        'photos' => $photos
                    ];
                }
            }
        } catch (\Throwable $e) {
            // pri chybe DB ukážeme prázdnu galériu
            $albums = [];
        }

        return $this->html(['albums' => $albums]);
    }
    public function registrationPage(Request $request): Response
    {
        $success = null;
        $error = null;
        $userNotRegistered = false; // príznak: email nie je zaregistrovaný používateľ

        // Prednastavenie formulára. Ak je prihlásený, predvyplníme údaje.
        $form = [
            'meno' => '',
            'priezvisko' => '',
            'email' => '',
            'pohlavie' => 'M'
        ];

        if ($this->user->isLoggedIn()) {
            $identity = $this->user->getIdentity();
            // DbUser poskytuje getFirstName/getLastName/getEmail
            if (method_exists($identity, 'getFirstName')) {
                $form['meno'] = $identity->getFirstName();
            }
            if (method_exists($identity, 'getLastName')) {
                $form['priezvisko'] = $identity->getLastName();
            }
            if (method_exists($identity, 'getEmail')) {
                $form['email'] = $identity->getEmail();
            }
            // Predvyplnenie pohlavia ak identita poskytne getGender
            if (method_exists($identity, 'getGender')) {
                $g = mb_strtoupper(trim($identity->getGender()), 'UTF-8');
                if ($g === 'Z') {
                    $g = 'Ž';
                }
                $form['pohlavie'] = ($g === 'Ž') ? 'Ž' : 'M';
            }
        }

        if ($request->isPost()) {
            // Načítaj a otrimuj vstupy
            $meno = trim((string)$request->post('meno'));
            $priezvisko = trim((string)$request->post('priezvisko'));
            $email = trim((string)$request->post('email'));
            $pohlavie = trim((string)$request->post('pohlavie'));
            $rok = date('Y');

            // Aktualizuj formulár s odoslanými hodnotami (pre opätovné zobrazenie)
            $form['meno'] = $meno;
            $form['priezvisko'] = $priezvisko;
            $form['email'] = $email;
            $form['pohlavie'] = $pohlavie ?: $form['pohlavie'];

            // Validácia na serveri: povinné polia
            if ($meno === '' || $priezvisko === '' || $email === '' || $pohlavie === '') {
                $error = 'Všetky polia sú povinné.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Zadaný email nemá správny formát.';
            } else {
                // Normalizuj pohlavie a povoľ len očakávané hodnoty (M / Ž)
                $pohlUp = mb_strtoupper($pohlavie, 'UTF-8');
                if (!in_array($pohlUp, ['M', 'Ž'], true)) {
                    $error = 'Neplatná hodnota pre pohlavie.';
                } else {
                    // Použi kanonické jednoznakové hodnoty uložené v DB (M alebo Ž)
                    $pohlavieStored = ($pohlUp === 'M') ? 'M' : 'Ž';

                    try {
                        $conn = \Framework\DB\Connection::getInstance();
                        // Nájde ID_roka pre aktuálny rok
                        $stmt = $conn->prepare('SELECT ID_roka FROM rokKonania WHERE rok = ? LIMIT 1');
                        $stmt->execute([$rok]);
                        $row = $stmt->fetch();
                        if ($row) {
                            $id_roka = $row['ID_roka'];
                        } else {
                            // Ak neexistuje, vytvor nový záznam
                            $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov, pocet_stanovisk) VALUES (?, ?, 0, 0)');
                            $stmt->execute([$rok, date('Y-m-d')]);
                            $id_roka = $conn->lastInsertId();
                        }

                        // --- Nové: zabráni duplicitnej registrácii rovnakého emailu v tom istom roku ---
                        $check = $conn->prepare('SELECT COUNT(*) AS cnt FROM Bezec WHERE email = ? AND ID_roka = ?');
                        $check->execute([$email, $id_roka]);
                        $exists = (int)$check->fetchColumn();
                        if ($exists > 0) {
                            $error = 'Tento email je už registrovaný na tohtoročný beh.';
                        } else {
                            // --- Nové: overenie, že email patrí registrovanému používateľovi ---
                            $u = $conn->prepare('SELECT COUNT(*) FROM Pouzivatelia WHERE email = ?');
                            $u->execute([$email]);
                            $userCount = (int)$u->fetchColumn();
                            if ($userCount === 0) {
                                // Užívateľ nie je registrovaný
                                $error = 'Zadaný email nie je zaregistrovaný ako používateľ. Prosím prihláste sa alebo si najprv vytvorte účet.';
                                $userNotRegistered = true;
                            } else {
                                // Uložíme bežca
                                $stmt = $conn->prepare('INSERT INTO Bezec (meno, priezvisko, email, pohlavie, ID_roka) VALUES (?, ?, ?, ?, ?)');
                                $stmt->execute([$meno, $priezvisko, $email, $pohlavieStored, $id_roka]);
                                // Aktualizujeme pocet_ucastnikov v rokKonania
                                $stmt = $conn->prepare('SELECT COUNT(*) AS pocet FROM Bezec WHERE ID_roka = ?');
                                $stmt->execute([$id_roka]);
                                $pocet = $stmt->fetch()['pocet'];
                                $stmt = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                                $stmt->execute([$pocet, $id_roka]);
                                $success = 'Registrácia prebehla úspešne!';
                            }
                        }
                    } catch (\PDOException $e) {
                        // Duplicate-entry pre unique constraint (MySQL 1062 / SQLSTATE 23000)
                        $sqlState = $e->getCode();
                        $mysqlErrNo = null;
                        if (is_array($e->errorInfo) && isset($e->errorInfo[1])) {
                            $mysqlErrNo = $e->errorInfo[1];
                        }
                        // Ak zlyhá FK (referenčný) MySQL error 1452
                        if ($sqlState === '23000' && $mysqlErrNo === 1452) {
                            $error = 'Zadaný email nie je zaregistrovaný ako používateľ. Prosím prihláste sa alebo si najprv vytvorte účet.';
                            $userNotRegistered = true;
                        } elseif ($sqlState === '23000' || $mysqlErrNo === 1062) {
                            $error = 'Tento email je už registrovaný na tohtoročný beh.';
                        } else {
                            $error = 'Chyba pri registrácii: ' . $e->getMessage();
                        }
                    } catch (\Exception $e) {
                        $error = 'Chyba pri registrácii: ' . $e->getMessage();
                    }
                }
            }
        }
        return $this->html([
            'success' => $success,
            'error' => $error,
            'form' => $form,
            'userNotRegistered' => $userNotRegistered
        ]);
    }
    public function resultsPage(Request $request): Response
    {
        $conn = \Framework\DB\Connection::getInstance();
        $resultsYear = null;
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $s = $conn->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
                $s->execute(['results_year']);
                $val = $s->fetchColumn();
                if ($val !== false && $val !== null && $val !== '') {
                    $resultsYear = $val;
                }
            }
        } catch (\Throwable $e) {
            $resultsYear = null;
        }

        $maleResults = [];
        $femaleResults = [];
        $resultsYearLabel = null;
        $finishedM = [];
        $finishedF = [];

        if ($resultsYear !== null) {
            // Načítať čitateľný rok z tabuľky rokKonania
            try {
                $sr = $conn->prepare('SELECT rok FROM rokKonania WHERE ID_roka = ? LIMIT 1');
                $sr->execute([$resultsYear]);
                $row = $sr->fetch();
                if ($row && isset($row['rok'])) {
                    $resultsYearLabel = $row['rok'];
                }
            } catch (\Throwable $e) {
                $resultsYearLabel = $resultsYear; // fallback na ID
            }

            $stmtM = $conn->prepare('SELECT meno, priezvisko, cas_dobehnutia FROM Bezec WHERE pohlavie = ? AND ID_roka = ? ORDER BY (cas_dobehnutia IS NULL), cas_dobehnutia ASC');
            $stmtM->execute(['M', $resultsYear]);
            $maleResults = $stmtM->fetchAll();

            $stmtF = $conn->prepare('SELECT meno, priezvisko, cas_dobehnutia FROM Bezec WHERE pohlavie = ? AND ID_roka = ? ORDER BY (cas_dobehnutia IS NULL), cas_dobehnutia ASC');
            $stmtF->execute(['Ž', $resultsYear]);
            $femaleResults = $stmtF->fetchAll();


            // Priprav zoznamy iba dokončených (neprázdny cas_dobehnutia)
            $finishedM = array_values(array_filter($maleResults, function($r) { return !empty($r['cas_dobehnutia']); }));
            $finishedF = array_values(array_filter($femaleResults, function($r) { return !empty($r['cas_dobehnutia']); }));


            // --- Kreditovanie používateľských účtov: odstránené ---
            // Predchádzajúca implementácia aktualizovala Pouzivatelia.zabehnute_kilometre a Pouzivatelia.vypite_piva.
            // Táto logika bola zámerne odstránená a nebude sa tu vykonávať zápis do DB.

        }

        return $this->html([
            'maleResults' => $maleResults,
            'femaleResults' => $femaleResults,
            'finishedM' => $finishedM,
            'finishedF' => $finishedF,
            'resultsYear' => $resultsYear,
            'resultsYearLabel' => $resultsYearLabel
        ]);
    }

    /**
     * Zobrazí profil prihláseného používateľa.
     * Ak nie je prihlásený, presmeruje na prihlasovanie.
     */
    public function profile(Request $request): Response
    {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect(\App\Configuration::LOGIN_URL);
        }

        $identity = $this->user->getIdentity();

        // Zistí, či je užívateľ registrovaný na aktuálny ročník
        $isRegisteredThisYear = false;
        try {
            $conn = \Framework\DB\Connection::getInstance();
            $currentYear = date('Y');

            // Nájde ID_roka pre aktuálny rok (ak existuje)
            $stmt = $conn->prepare('SELECT ID_roka FROM rokKonania WHERE rok = ? LIMIT 1');
            $stmt->execute([$currentYear]);
            $row = $stmt->fetch();
            if ($row && isset($row['ID_roka'])) {
                $idRoka = $row['ID_roka'];
                // Skontroluje registráciu v Bezec podľa emailu
                $stmt2 = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE email = ? AND ID_roka = ?');
                $stmt2->execute([$identity->getEmail(), $idRoka]);
                $cnt = (int)$stmt2->fetchColumn();
                $isRegisteredThisYear = ($cnt > 0);
            }
        } catch (\Throwable $e) {
            // Pri chybe DB predpokladaj, že nie je registrovaný (bezpečné správanie)
            $isRegisteredThisYear = false;
        }

        return $this->html(compact('identity', 'isRegisteredThisYear'));
    }
}
