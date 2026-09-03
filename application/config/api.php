<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['ppdwk_token'] = getenv('PPDWK_TOKEN') ?: '';
$config['api_token'] = getenv('API_TOKEN') ?: 'absensi_api_token_secret_xyz';
$config['pelanggaran_api_url'] = getenv('PELANGGARAN_API_URL') ?: 'http://bk2.test/api';
$config['pelanggaran_api_token'] = getenv('PELANGGARAN_API_TOKEN') ?: 'sipesan_dwk_bearer_secret_token_key_2026';

