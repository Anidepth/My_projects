<?php
$admin_hash = '$2y$10$yaq1ls3DwZIWAJtTinWOj.rWaUmp/E9GVXi1VFc6PgBEcIwdedEua';
$user_hash = '$2y$10$IkcaRLF6A3lXkjr13cdEEOzaxOzQuknuLie7ubvXRQL6TtfYf9yxm';
echo password_verify('admin123', $admin_hash) ? 'PASS' : 'FAIL';
echo "\n";
echo password_verify('user123', $user_hash) ? 'PASS' : 'FAIL';
