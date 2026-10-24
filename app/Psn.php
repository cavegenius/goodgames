<?php

namespace App;

use PlayStation\Client;

class Psn
{
    /**
     * Exchange an NPSSO token for a refresh token.
     */
    public function firstLogin(string $accountId, string $npsso): ?string
    {
        $client = new Client();
        $client->loginWithNpsso($npsso);

        return $client->getRefreshToken()?->getToken();
    }

    public function login(): Client
    {
        $client = new Client();
        $client->loginWithNpsso((string) config('keys.psnKey'));

        return $client;
    }

    public function import(string $accountId): array
    {
        $titles = $this->login()
            ->users()
            ->find($accountId)
            ->trophyTitles()
            ->hasTrophyGroups();

        $games = [];

        foreach ($titles as $title) {
            foreach ($title->trophyGroups() as $group) {
                $games[] = $group->name();
            }
        }

        return $games;
    }
}
