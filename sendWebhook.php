<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['SkidAlert' => 'why are you here']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);


if (!$data || !isset($data['ip']) || !isset($data['country']) || !isset($data['os'])) {
    http_response_code(400);
    echo json_encode(['SkidAlert' => 'Eksik veya geçersiz veri']);
    exit;
}

$webhookUrl = 'https://discord.com/api/webhooks/1398963420853960846/v3Fg7a0rCRnHgwP195pTzyRbGz1s7rzHf5LerCeFUBGyk5tQ4fUGMzqGDkOjmAOfZXGM';

$ip         = $data['ip'];
$country    = $data['country'];
$countryCode= strtoupper($data['countryCode'] ?? '');
$os         = $data['os'];
$browser    = $data['browser'];

// Bayrak emojisi
$flag = '';
foreach (str_split($countryCode) as $c) {
  $flag .= mb_convert_encoding('&#'.(127397+ord($c)).';', 'UTF-8', 'HTML-ENTITIES');
}

$embed = [
  'title'     => '🎉 Dosya İndirildi!',
  'color'     => hexdec('4A90E2'),
  'author'    => [
    'name'     => 'Toravox Downloader',
    'icon_url' => 'https://media.discordapp.net/attachments/1397430552960827523/1398963596939104256/hq720.jpg?ex=688745e0&is=6885f460&hm=ecb029e5f47b1ee731d170ee515dcc64853a18592ddcebb42cd874e81206d008&=&format=webp&width=755&height=425'
  ],
  'thumbnail' => [
    'url'      => 'https://media.discordapp.net/attachments/1397430552960827523/1398963596939104256/hq720.jpg?ex=688745e0&is=6885f460&hm=ecb029e5f47b1ee731d170ee515dcc64853a18592ddcebb42cd874e81206d008&=&format=webp&width=755&height=425'
  ],
  'fields'    => [
    ['name'=>'📥 IP Adresi',       'value'=>$ip,      'inline'=>true],
    ['name'=>'🌍 Konum',           'value'=>"$country $flag", 'inline'=>true],
    ['name'=>'💻 İşletim Sistemi', 'value'=>$os,      'inline'=>true],
    ['name'=>'🌐 Tarayıcı',        'value'=>$browser, 'inline'=>true]
  ],
  'footer'    => [
    'text'     => 'Toravox | İndirme Bilgisi',
    'icon_url' => 'https://media.discordapp.net/attachments/1397430552960827523/1398963596939104256/hq720.jpg?ex=688745e0&is=6885f460&hm=ecb029e5f47b1ee731d170ee515dcc64853a18592ddcebb42cd874e81206d008&=&format=webp&width=755&height=425'
  ],
  'timestamp' => date('c')
];

$payload = json_encode(['embeds'=>[$embed]]);

$ch = curl_init($webhookUrl);
curl_setopt_array($ch, [
  CURLOPT_POST        => true,
  CURLOPT_POSTFIELDS  => $payload,
  CURLOPT_HTTPHEADER  => ['Content-Type: application/json'],
  CURLOPT_RETURNTRANSFER => true
]);
curl_exec($ch);
curl_close($ch);

echo json_encode(['status'=>'ok']);
