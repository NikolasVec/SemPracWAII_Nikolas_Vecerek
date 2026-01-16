<?php

namespace Framework\Auth;

use Framework\Core\IIdentity;

/**
 * Class DbAuthenticator
 * Authenticates users against the `Pouzivatelia` table in the database.
 */
class DbAuthenticator extends SessionAuthenticator
{
    public function __construct(\Framework\Core\App $app)
    {
        parent::__construct($app);
    }

    protected function authenticate(string $username, string $password): ?IIdentity
    {
        // Try to find user by email or by username (meno)
        $db = $this->app->getDb();
        // Only allow login by email (require unique email). Fetch admin flag and usage stats.
        $stmt = $db->prepare('SELECT ID_pouzivatela AS id, meno, priezvisko, email, heslo, admin, zabehnute_kilometre, vypite_piva FROM Pouzivatelia WHERE email = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $hash = $row['heslo'] ?? '';
        if ($hash !== '' && password_verify($password, $hash)) {
            $admin = isset($row['admin']) ? (bool)$row['admin'] : false;
            $kilometres = isset($row['zabehnute_kilometre']) ? (float)$row['zabehnute_kilometre'] : 0.0;
            $beers = isset($row['vypite_piva']) ? (int)$row['vypite_piva'] : 0;
            return new DbUser((int)$row['id'], $row['meno'] ?? '', $row['priezvisko'] ?? '', $row['email'] ?? '', $admin, $kilometres, $beers);
        }

        return null;
    }
}
