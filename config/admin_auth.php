<?php
// Hash SHA-256 dari kode lembaga. Kode asli tidak disimpan di database.
const ADMIN_INSTITUTION_CODE_HASH = 'f8c2f59a32c9f2d30b742ea8411031e335c16d432eda09c64183b0499c9e0f81';

function validAdminInstitutionCode($code) {
    return hash_equals(ADMIN_INSTITUTION_CODE_HASH, hash('sha256', trim($code)));
}
