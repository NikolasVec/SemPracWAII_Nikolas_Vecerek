<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\DB\Connection;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class AdminController
 *
 * This controller manages admin-related actions within the application.It extends the base controller functionality
 * provided by BaseController.
 *
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    /**
     * Authorizes actions in this controller.
     *
     * This method checks if the user is logged in, allowing or denying access to specific actions based
     * on the authentication state.
     *
     * @param string $action The name of the action to authorize.
     * @return bool Returns true if the user is logged in; false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        // Only allow access to admin controller for logged-in users with admin privileges.
        return $this->user->isLoggedIn() && $this->user->isAdmin();
    }

    /**
     * Displays the index page of the admin panel.
     *
     * This action requires authorization. It returns an HTML response for the admin dashboard or main page.
     *
     * @return Response Returns a response object containing the rendered HTML.
     */
    public function index(Request $request): Response
    {
        $conn = Connection::getInstance();
        $bezci = $conn->query('SELECT * FROM Bezec')->fetchAll();
        $roky = $conn->query('SELECT * FROM rokKonania')->fetchAll();
        $stanoviska = $conn->query('SELECT * FROM Stanovisko')->fetchAll();

        // Try to load currently selected year for results from settings table (if exists)
        $currentResultsYear = null;
        try {
            // ensure settings table may not exist; select safely
            $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $s = $conn->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
                $s->execute(['results_year']);
                $val = $s->fetchColumn();
                if ($val !== false && $val !== null && $val !== '') {
                    $currentResultsYear = $val;
                }
            }
        } catch (\Throwable $e) {
            // ignore - settings table may not exist
            $currentResultsYear = null;
        }

        // Fetch albums for gallery management if table exists
        $albums = [];
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'albums'");
            $albumsTableExists = $stmt->fetchColumn() !== false;
            if ($albumsTableExists) {
                $albums = $conn->query('SELECT * FROM albums ORDER BY created_at DESC')->fetchAll();
            }
        } catch (\Throwable $e) {
            $albums = [];
        }

        // compute upload/post size limits (convert shorthand like '2M' to bytes)
        $parseIniBytes = function($val) {
            $val = trim($val);
            if ($val === '') return 0;
            $last = strtolower($val[strlen($val)-1]);
            $num = (int)$val;
            switch ($last) {
                case 'g': $num *= 1024 * 1024 * 1024; break;
                case 'm': $num *= 1024 * 1024; break;
                case 'k': $num *= 1024; break;
                default: // no suffix
            }
            return $num;
        };
        $uploadMaxBytes = $parseIniBytes(ini_get('upload_max_filesize') ?: '0');
        $postMaxBytes = $parseIniBytes(ini_get('post_max_size') ?: '0');

        return $this->html([
            'bezci' => $bezci,
            'roky' => $roky,
            'stanoviska' => $stanoviska,
            'currentResultsYear' => $currentResultsYear,
            'albums' => $albums,
            'upload_max_bytes' => $uploadMaxBytes,
            'post_max_bytes' => $postMaxBytes
        ]);
    }

    /**
     * Handles AJAX add requests for Bezec, rokKonania, Stanovisko.
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        // simple debug helper (optional)
        // file_put_contents(__DIR__ . '/debug.log', print_r($_POST, true) . PHP_EOL, FILE_APPEND);

        $section = $_GET['section'] ?? null;
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $conn = Connection::getInstance();

        if ($method !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        try {
            if ($section === 'bezci') {
                $meno = trim($_POST['meno'] ?? '');
                $priezvisko = trim($_POST['priezvisko'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pohlavie = trim($_POST['pohlavie'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;
                $cas = trim($_POST['cas_dobehnutia'] ?? '');

                // Normalize time: accept H:MM or H:MM:SS and convert to HH:MM:SS; empty -> null
                if ($cas === '') {
                    $casTime = null;
                } else {
                    if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $cas, $m)) {
                        $h = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $i = $m[2];
                        $s = isset($m[3]) ? $m[3] : '00';
                        $casTime = "$h:$i:$s";
                    } else {
                        return $this->json(['success' => false, 'message' => 'Neplatný formát času (očakávané H:MM alebo H:MM:SS).']);
                    }
                }

                if ($meno === '' || $priezvisko === '' || $email === '' || $pohlavie === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO Bezec (meno, priezvisko, email, pohlavie, cas_dobehnutia, ID_roka) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $casTime, $ID_roka]);

                // update participant count for the year
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true]);
            } elseif ($section === 'roky') {
                $rok = trim($_POST['rok'] ?? '');
                $datum = trim($_POST['datum_konania'] ?? '');

                if ($rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov) VALUES (?, ?, 0)');
                $stmt->execute([$rok, $datum]);
                $ID_roka = $conn->query('SELECT LAST_INSERT_ID()')->fetchColumn();

                // compute participants count (should be zero just after insert, but kept for consistency)
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true]);
            } elseif ($section === 'stanoviska') {
                $nazov = trim($_POST['nazov'] ?? '');
                $poloha = trim($_POST['poloha'] ?? '');
                $popis = trim($_POST['popis'] ?? '');
                $mapa_odkaz = trim($_POST['mapa_odkaz'] ?? '');
                $obrazok_odkaz = trim($_POST['obrazok_odkaz'] ?? '');
                // optional relative positions on the admin map image (0..1)
                $x_pos = trim($_POST['x_pos'] ?? '');
                $y_pos = trim($_POST['y_pos'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;

                if ($nazov === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                // convert empty strings to NULL so DB can store default NULL
                $mapaVal = $mapa_odkaz === '' ? null : $mapa_odkaz;
                $obrazokVal = $obrazok_odkaz === '' ? null : $obrazok_odkaz;
                $xVal = ($x_pos === '' ? null : (is_numeric($x_pos) ? $x_pos : null));
                $yVal = ($y_pos === '' ? null : (is_numeric($y_pos) ? $y_pos : null));

                $stmt = $conn->prepare('INSERT INTO Stanovisko (nazov, poloha, popis, mapa_odkaz, obrazok_odkaz, ID_roka, x_pos, y_pos) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$nazov, $poloha, $popis, $mapaVal, $obrazokVal, $ID_roka, $xVal, $yVal]);

                return $this->json(['success' => true]);
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a record by section and id
     */
    public function delete(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $stmt = $conn->prepare('DELETE FROM Bezec WHERE ID_bezca = ?');
                $stmt->execute([$id]);
            } elseif ($section === 'roky') {
                $stmt = $conn->prepare('DELETE FROM rokKonania WHERE ID_roka = ?');
                $stmt->execute([$id]);
            } elseif ($section === 'stanoviska') {
                $stmt = $conn->prepare('DELETE FROM Stanovisko WHERE ID_stanoviska = ?');
                $stmt->execute([$id]);
            } elseif ($section === 'photos') {
                // delete single photo by ID: remove file and DB row
                $stmt = $conn->prepare('SELECT album_id, filename FROM photos WHERE ID_photo = ? LIMIT 1');
                $stmt->execute([$id]);
                $row = $stmt->fetch();
                if ($row) {
                    $albumId = $row['album_id'];
                    $filename = $row['filename'];
                    $projectRoot = dirname(dirname(__DIR__));
                    $filePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR . intval($albumId) . DIRECTORY_SEPARATOR . $filename;
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                    $del = $conn->prepare('DELETE FROM photos WHERE ID_photo = ?');
                    $del->execute([$id]);
                } else {
                    return $this->json(['success' => false, 'message' => 'Fotka neexistuje.']);
                }
            } elseif ($section === 'albums') {
                // delete album and all associated photos/files
                $albumId = $id;
                // fetch photos
                $stmt = $conn->prepare('SELECT filename FROM photos WHERE album_id = ?');
                $stmt->execute([$albumId]);
                $photos = $stmt->fetchAll();
                $projectRoot = dirname(dirname(__DIR__));
                $albumDir = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR . intval($albumId);
                // delete files
                foreach ($photos as $ph) {
                    $filePath = $albumDir . DIRECTORY_SEPARATOR . ($ph['filename'] ?? '');
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
                // remove photos records
                $delPh = $conn->prepare('DELETE FROM photos WHERE album_id = ?');
                $delPh->execute([$albumId]);
                // remove album folder if empty
                if (is_dir($albumDir)) {
                    @rmdir($albumDir);
                }
                // delete album row
                $stmt = $conn->prepare('DELETE FROM albums WHERE ID_album = ?');
                $stmt->execute([$albumId]);
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get a single record for edit
     */
    public function get(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $stmt = $conn->prepare('SELECT * FROM Bezec WHERE ID_bezca = ?');
            } elseif ($section === 'roky') {
                $stmt = $conn->prepare('SELECT * FROM rokKonania WHERE ID_roka = ?');
            } elseif ($section === 'stanoviska') {
                $stmt = $conn->prepare('SELECT * FROM Stanovisko WHERE ID_stanoviska = ?');
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if (!$item) {
                return $this->json(['success' => false, 'message' => 'Záznam neexistuje.']);
            }

            return $this->json(['success' => true, 'item' => $item]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a record by section
     */
    public function update(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_POST['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $meno = trim($_POST['meno'] ?? '');
                $priezvisko = trim($_POST['priezvisko'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pohlavie = trim($_POST['pohlavie'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;
                $cas = trim($_POST['cas_dobehnutia'] ?? '');

                if ($cas === '') {
                    $casTime = null;
                } else {
                    if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $cas, $m)) {
                        $h = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $i = $m[2];
                        $s = isset($m[3]) ? $m[3] : '00';
                        $casTime = "$h:$i:$s";
                    } else {
                        return $this->json(['success' => false, 'message' => 'Neplatný formát času (očakávané H:MM alebo H:MM:SS).']);
                    }
                }

                if ($meno === '' || $priezvisko === '' || $email === '' || $pohlavie === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE Bezec SET meno=?, priezvisko=?, email=?, pohlavie=?, cas_dobehnutia=?, ID_roka=? WHERE ID_bezca=?');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $casTime, $ID_roka, $id]);

                // update participant counts for affected years (simple approach: update the target year)
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

            } elseif ($section === 'roky') {
                $rok = trim($_POST['rok'] ?? '');
                $datum = trim($_POST['datum_konania'] ?? '');

                if ($rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE rokKonania SET rok=?, datum_konania=? WHERE ID_roka=?');
                $stmt->execute([$rok, $datum, $id]);

            } elseif ($section === 'stanoviska') {
                $nazov = trim($_POST['nazov'] ?? '');
                $poloha = trim($_POST['poloha'] ?? '');
                $popis = trim($_POST['popis'] ?? '');
                $mapa_odkaz = trim($_POST['mapa_odkaz'] ?? '');
                $obrazok_odkaz = trim($_POST['obrazok_odkaz'] ?? '');
                $x_pos = trim($_POST['x_pos'] ?? '');
                $y_pos = trim($_POST['y_pos'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;

                if ($nazov === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $mapaVal = $mapa_odkaz === '' ? null : $mapa_odkaz;
                $obrazokVal = $obrazok_odkaz === '' ? null : $obrazok_odkaz;
                $xVal = ($x_pos === '' ? null : (is_numeric($x_pos) ? $x_pos : null));
                $yVal = ($y_pos === '' ? null : (is_numeric($y_pos) ? $y_pos : null));

                $stmt = $conn->prepare('UPDATE Stanovisko SET nazov=?, poloha=?, popis=?, mapa_odkaz=?, obrazok_odkaz=?, ID_roka=?, x_pos=?, y_pos=? WHERE ID_stanoviska=?');
                $stmt->execute([$nazov, $poloha, $popis, $mapaVal, $obrazokVal, $ID_roka, $xVal, $yVal, $id]);

            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // New action to set which year is used on the public results page
    public function setResultsYear(Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        $id = $_POST['id'] ?? null; // ID_roka or empty to clear
        try {
            $conn = Connection::getInstance();
            // create settings table if not exists
            $conn->exec("CREATE TABLE IF NOT EXISTS settings (k VARCHAR(64) PRIMARY KEY, v VARCHAR(255) NULL)");

            if ($id === null || $id === '') {
                // remove the setting
                $stmt = $conn->prepare('DELETE FROM settings WHERE k = ?');
                $stmt->execute(['results_year']);
            } else {
                // upsert setting
                // Try update first
                $u = $conn->prepare('UPDATE settings SET v = ? WHERE k = ?');
                $u->execute([$id, 'results_year']);
                if ($u->rowCount() === 0) {
                    $i = $conn->prepare('INSERT INTO settings (k, v) VALUES (?, ?)');
                    $i->execute(['results_year', $id]);
                }
            }

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- New gallery-related actions ---

    /**
     * Create a new album (AJAX)
     */
    public function createAlbum(Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            return $this->json(['success' => false, 'message' => 'Názov albumu je povinný.']);
        }

        try {
            $conn = Connection::getInstance();
            // create albums table if not exists
            $conn->exec("CREATE TABLE IF NOT EXISTS albums (ID_album INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), slug VARCHAR(255), description TEXT, created_at DATETIME)");

            // generate simple slug
            $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
            $slug = trim($slug, '-');

            $stmt = $conn->prepare('INSERT INTO albums (name, slug, description, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$name, $slug, $description]);
            $id = $conn->lastInsertId();

            return $this->json(['success' => true, 'id' => $id]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Upload photos to an album (AJAX multipart/form-data)
     */
    public function uploadPhoto(Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        $albumId = $_POST['album_id'] ?? null;
        // If $_POST and $_FILES are empty on a POST request, likely PHP's post_max_size/upload_max_filesize were exceeded
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
            return $this->json(['success' => false, 'message' => 'Žiadne údaje boli odoslané. Skontrolujte nastavenia PHP (post_max_size, upload_max_filesize) a veľkosť nahrávaných súborov.']);
        }
        if (!$albumId) {
            return $this->json(['success' => false, 'message' => 'Chýba ID albumu.']);
        }

        try {
            $conn = Connection::getInstance();
            // create photos table if not exists
            $conn->exec("CREATE TABLE IF NOT EXISTS photos (ID_photo INT AUTO_INCREMENT PRIMARY KEY, album_id INT, filename VARCHAR(255), original_name VARCHAR(255), created_at DATETIME)");

            // prepare storage directory: public/images/gallery/{albumId}
            $projectRoot = dirname(dirname(__DIR__));
            $basePublic = $projectRoot . DIRECTORY_SEPARATOR . 'public';
            $galleryBase = $basePublic . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'gallery';
            if (!is_dir($galleryBase)) {
                if (!mkdir($galleryBase, 0755, true) && !is_dir($galleryBase)) {
                    throw new \RuntimeException('Nepodarilo sa vytvoriť priečinok galérie: ' . $galleryBase);
                }
            }
            $albumDir = $galleryBase . DIRECTORY_SEPARATOR . intval($albumId);
            if (!is_dir($albumDir)) {
                if (!mkdir($albumDir, 0755, true) && !is_dir($albumDir)) {
                    throw new \RuntimeException('Nepodarilo sa vytvoriť priečinok albumu: ' . $albumDir);
                }
            }

            $uploaded = [];
            $errors = [];

            // Normalize various possible $_FILES shapes: photos[] (multiple), single photos, or other keys
            if (!empty($_FILES['photos'])) {
                $files = $_FILES['photos'];
                // multiple
                if (is_array($files['name'])) {
                    $count = count($files['name']);
                    for ($i = 0; $i < $count; $i++) {
                        $err = $files['error'][$i];
                        if ($err !== UPLOAD_ERR_OK) {
                            $errors[] = "Upload error for file index $i: code $err";
                            continue;
                        }
                        $tmp = $files['tmp_name'][$i];
                        if (!is_uploaded_file($tmp)) {
                            $errors[] = "Dočasný súbor nie je platný pre index $i";
                            continue;
                        }
                        $orig = basename($files['name'][$i]);
                        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                        if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
                            $errors[] = "Nepovolená prípona pre súbor: $orig";
                            continue;
                        }
                        $newName = uniqid('', true) . '.' . $ext;
                        $target = $albumDir . DIRECTORY_SEPARATOR . $newName;
                        if (!move_uploaded_file($tmp, $target)) {
                            $errors[] = "Nepodarilo sa presunúť súbor: $orig";
                            continue;
                        }
                        $stmt = $conn->prepare('INSERT INTO photos (album_id, filename, original_name, created_at) VALUES (?, ?, ?, NOW())');
                        $stmt->execute([intval($albumId), $newName, $orig]);
                        $uploaded[] = $newName;
                    }
                } else {
                    // single file in photos
                    $err = $files['error'];
                    if ($err === UPLOAD_ERR_OK) {
                        $tmp = $files['tmp_name'];
                        if (is_uploaded_file($tmp)) {
                            $orig = basename($files['name']);
                            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                                $newName = uniqid('', true) . '.' . $ext;
                                $target = $albumDir . DIRECTORY_SEPARATOR . $newName;
                                if (move_uploaded_file($tmp, $target)) {
                                    $stmt = $conn->prepare('INSERT INTO photos (album_id, filename, original_name, created_at) VALUES (?, ?, ?, NOW())');
                                    $stmt->execute([intval($albumId), $newName, $orig]);
                                    $uploaded[] = $newName;
                                } else {
                                    $errors[] = "Nepodarilo sa presunúť súbor: $orig";
                                }
                            } else {
                                $errors[] = "Nepovolená prípona pre súbor: $orig";
                            }
                        } else {
                            $errors[] = 'Dočasný súbor nie je platný.';
                        }
                    } else {
                        $errors[] = 'Chyba pri nahrávaní súboru (kód ' . $err . ')';
                    }
                }
            } else {
                // No 'photos' key - try to find any uploaded files
                foreach ($_FILES as $key => $files) {
                    if (empty($files)) continue;
                    if (is_array($files['name'])) {
                        $count = count($files['name']);
                        for ($i = 0; $i < $count; $i++) {
                            $err = $files['error'][$i];
                            if ($err !== UPLOAD_ERR_OK) { $errors[] = "Upload error for $key index $i: code $err"; continue; }
                            $tmp = $files['tmp_name'][$i];
                            if (!is_uploaded_file($tmp)) { $errors[] = "Dočasný súbor nie je platný pre $key index $i"; continue; }
                            $orig = basename($files['name'][$i]);
                            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                            if (!in_array($ext, ['jpg','jpeg','png','gif'])) { $errors[] = "Nepovolená prípona pre súbor: $orig"; continue; }
                            $newName = uniqid('', true) . '.' . $ext;
                            $target = $albumDir . DIRECTORY_SEPARATOR . $newName;
                            if (!move_uploaded_file($tmp, $target)) { $errors[] = "Nepodarilo sa presunúť súbor: $orig"; continue; }
                            $stmt = $conn->prepare('INSERT INTO photos (album_id, filename, original_name, created_at) VALUES (?, ?, ?, NOW())');
                            $stmt->execute([intval($albumId), $newName, $orig]);
                            $uploaded[] = $newName;
                        }
                    } else {
                        $err = $files['error'];
                        if ($err !== UPLOAD_ERR_OK) { $errors[] = "Upload error for $key: code $err"; continue; }
                        $tmp = $files['tmp_name'];
                        if (!is_uploaded_file($tmp)) { $errors[] = "Dočasný súbor nie je platný pre $key"; continue; }
                        $orig = basename($files['name']);
                        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                        if (!in_array($ext, ['jpg','jpeg','png','gif'])) { $errors[] = "Nepovolená prípona pre súbor: $orig"; continue; }
                        $newName = uniqid('', true) . '.' . $ext;
                        $target = $albumDir . DIRECTORY_SEPARATOR . $newName;
                        if (!move_uploaded_file($tmp, $target)) { $errors[] = "Nepodarilo sa presunúť súbor: $orig"; continue; }
                        $stmt = $conn->prepare('INSERT INTO photos (album_id, filename, original_name, created_at) VALUES (?, ?, ?, NOW())');
                        $stmt->execute([intval($albumId), $newName, $orig]);
                        $uploaded[] = $newName;
                    }
                }
            }

            if (empty($uploaded)) {
                $msg = 'Nepodarilo sa nahrať žiadne obrázky.';
                if (!empty($errors)) {
                    $msg .= ' Dôvody: ' . implode(' | ', $errors);
                }

                // write debug log to storage/logs/uploads.log to help troubleshooting
                try {
                    $logDir = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
                    if (!is_dir($logDir)) {
                        @mkdir($logDir, 0755, true);
                    }
                    $logFile = $logDir . DIRECTORY_SEPARATOR . 'uploads.log';
                    $entry = '[' . date('Y-m-d H:i:s') . '] album=' . intval($albumId) . ' msg="' . addslashes($msg) . '" errors="' . addslashes(implode(' | ', $errors)) . '" filesSnapshot="' . addslashes(json_encode(array_map(function($f){ return ['name'=>is_array($f['name'])? $f['name'] : $f['name'], 'error'=>is_array($f['error'])? $f['error'] : $f['error']]; }, $_FILES))) . '"' . PHP_EOL;
                    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
                } catch (\Throwable $e) {
                    // ignore logging errors
                }

                return $this->json(['success' => false, 'message' => $msg]);
            }

            return $this->json(['success' => true, 'files' => $uploaded, 'errors' => $errors]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * List photos for an album (AJAX)
     */
    public function listPhotos(Request $request): Response
    {
        $albumId = $_GET['album_id'] ?? null;
        if (!$albumId) {
            return $this->json(['success' => false, 'message' => 'Chýba ID albumu.']);
        }

        try {
            $conn = Connection::getInstance();
            $stmt = $conn->prepare('SELECT ID_photo, album_id, filename, original_name, created_at FROM photos WHERE album_id = ? ORDER BY created_at ASC');
            $stmt->execute([$albumId]);
            $photos = $stmt->fetchAll();
            return $this->json(['success' => true, 'photos' => $photos]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}
