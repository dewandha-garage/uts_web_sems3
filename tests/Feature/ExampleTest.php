<?php

test('halaman utama mengarahkan pengunjung tamu ke login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
