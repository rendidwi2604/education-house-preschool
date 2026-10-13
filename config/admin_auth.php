<?php
// Hash SHA-256 dari kode lembaga. Kode asli tidak disimpan di database.
// Kode lembaga saat ini: EduHouse2026
const ADMIN_INSTITUTION_CODE_HASH = 'a6a6a3864ba9a1047cbc413a51b40cbedbe50be70dcffd3d4f1a950e4cf9ade2';

function validAdminInstitutionCode($code) {
    return hash_equals(ADMIN_INSTITUTION_CODE_HASH, hash('sha256', trim($code)));
}
