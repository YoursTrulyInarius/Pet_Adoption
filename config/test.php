<?php
$doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$app_root = str_replace('\\', '/', dirname(__DIR__));
$base_url = str_replace($doc_root, '', $app_root);
echo "doc_root: $doc_root\n";
echo "app_root: $app_root\n";
echo "base_url: $base_url\n";
