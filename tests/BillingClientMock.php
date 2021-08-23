<?php


namespace App\Tests;

use App\Security\User;
use App\Service\BillingClient;


class BillingClientMock extends BillingClient
{
    public function login(array $credentials)
    {
        $user = new User();

        $user->setEmail($credentials['email']);
        $user->setApiToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mjk3MjE3MzQsImV4cCI6MTYyOTcyNTMzNCwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFkbWluLmNvbSJ9.OtmVqzM4gkH1F82aWGvIe_oKueS9T_1HzTg4SGS68UkKHUX0-pPTFAfuOxbtZXCXqhDLbUnaBdOLY3BGLYJOLWba-xCKKNB4csMhLIhNDsiFyBZXqK8KpG5HhIBFgZOW_-L0Z6_T_HNHe_pDmE7gGZS5ZRloPfFmtIK-5QwTKhpFIKC7UuLp-RGZLSFpdYFwsZaVzglebtg7-mo-eCL31fNtjAbEQio1gE-068lQ7ZsodmQDqDyF7vI5_SkXXM23sAfTGq3bKaByYan8p4dFlC2LIsrPnzTPy9TKk1ij3OlGUprQA48RIUxHW5qccXRRBsymLSkfJz4QJH1WT4j07rRtjJGUpjGRYq5LSPj9WPF8Bi2YOrPcUo9CKOl8x2Z4pwzeEkurLYkXbPLbcT_R0z5K8NEIw-v7juYjV7BlXaNVeoUaX9la8bHXKC3NXQBQbV8e78DzSp22AGBkGCkqIlhd7SSKXcm3EmXFfTZgRy5w_s4B8jkLuAQdqmB1YochFpWRLR94TePrDXkCYcL2UoM1L81pdmr4-cTlv9OtkBytbNETa2HJZ3Ty1T2y_xHsO0HpCsxKM0IgxkiGToWR_frTwFRguxTn7mfVvRUt9bD1odvbQgDyyyw_QlYKMK-7yB0rKXtsx8FK7lrjxAN-1DTnhGOF20QAxEbFF1Q9HOM");
        $user->setRefreshToken("qwtqewtwt");
        $roleAdmin[] = 'ROLE_SUPER_ADMIN';
        $roleUser[] = 'ROLE_USER';

        if (str_contains($credentials['email'], 'admin')) {
            $user->setRoles($roleAdmin);
        }
        else{
            $user->setRoles($roleUser);
        }

        return $user;
    }

    public function register(array $credentials, User $user)
    {

        $user = new User();

        $user->setEmail($credentials['email']);
        $user->setApiToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mjk3MjE3MzQsImV4cCI6MTYyOTcyNTMzNCwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFkbWluLmNvbSJ9.OtmVqzM4gkH1F82aWGvIe_oKueS9T_1HzTg4SGS68UkKHUX0-pPTFAfuOxbtZXCXqhDLbUnaBdOLY3BGLYJOLWba-xCKKNB4csMhLIhNDsiFyBZXqK8KpG5HhIBFgZOW_-L0Z6_T_HNHe_pDmE7gGZS5ZRloPfFmtIK-5QwTKhpFIKC7UuLp-RGZLSFpdYFwsZaVzglebtg7-mo-eCL31fNtjAbEQio1gE-068lQ7ZsodmQDqDyF7vI5_SkXXM23sAfTGq3bKaByYan8p4dFlC2LIsrPnzTPy9TKk1ij3OlGUprQA48RIUxHW5qccXRRBsymLSkfJz4QJH1WT4j07rRtjJGUpjGRYq5LSPj9WPF8Bi2YOrPcUo9CKOl8x2Z4pwzeEkurLYkXbPLbcT_R0z5K8NEIw-v7juYjV7BlXaNVeoUaX9la8bHXKC3NXQBQbV8e78DzSp22AGBkGCkqIlhd7SSKXcm3EmXFfTZgRy5w_s4B8jkLuAQdqmB1YochFpWRLR94TePrDXkCYcL2UoM1L81pdmr4-cTlv9OtkBytbNETa2HJZ3Ty1T2y_xHsO0HpCsxKM0IgxkiGToWR_frTwFRguxTn7mfVvRUt9bD1odvbQgDyyyw_QlYKMK-7yB0rKXtsx8FK7lrjxAN-1DTnhGOF20QAxEbFF1Q9HOM");
        $user->setRefreshToken("shshhdfhdhs");
        $roles[] = 'ROLE_USER';
        $user->setRoles($roles);
        return $user;
    }

    public function refreshToken(User $user)
    {
        return $user;
    }

}