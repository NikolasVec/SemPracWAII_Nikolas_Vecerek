<?php
namespace Framework\Support;

use Framework\DB\Connection;

class FooterDataProvider
{
    private $contactEmail;
    private $contactPhone;
    private $facebookUrl;
    private $instagramUrl;
    private $sponsors = [];

    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        try {
            $conn = Connection::getInstance();

            // load settings if table exists
            $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
            if ($stmt && $stmt->fetchColumn() !== false) {
                $s = $conn->prepare("SELECT k, v FROM settings WHERE k IN ('contact_email','contact_phone','facebook_url','instagram_url')");
                $s->execute();
                $rows = $s->fetchAll();
                foreach ($rows as $r) {
                    if ($r['k'] === 'contact_email') $this->contactEmail = $r['v'];
                    if ($r['k'] === 'contact_phone') $this->contactPhone = $r['v'];
                    if ($r['k'] === 'facebook_url') $this->facebookUrl = $r['v'];
                    if ($r['k'] === 'instagram_url') $this->instagramUrl = $r['v'];
                }
            }

            // load sponsors if table exists
            $stmt = $conn->query("SHOW TABLES LIKE 'sponsors'");
            if ($stmt && $stmt->fetchColumn() !== false) {
                $s2 = $conn->query('SELECT * FROM sponsors ORDER BY created_at ASC');
                $this->sponsors = $s2->fetchAll();
            }
        } catch (\Throwable $e) {
            // fail silently for footer; leave defaults
        }

        // fallback defaults
        if (empty($this->contactEmail)) $this->contactEmail = 'behpopivo@gmail.com';
        if (empty($this->contactPhone)) $this->contactPhone = '+421 900 000 000';
        if (empty($this->facebookUrl)) $this->facebookUrl = 'https://www.facebook.com/BehPoPivo?locale=sk_SK';
        if (empty($this->instagramUrl)) $this->instagramUrl = 'https://www.instagram.com/behpopivo_martin/';
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    public function getFacebookUrl(): string
    {
        return $this->facebookUrl;
    }

    public function getInstagramUrl(): string
    {
        return $this->instagramUrl;
    }

    public function getSponsors(): array
    {
        return $this->sponsors;
    }
}

