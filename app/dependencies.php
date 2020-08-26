<?php
declare(strict_types=1);

$container = $app->getContainer();

$container['cache'] = function ($c) {
    return new Slim\HttpCache\CacheProvider();
};


// Database adapter
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];
    $dsn = 'mysql:host=' . $db['host'];
    $dsn .= ';dbname=' . $db['name'];
    $dsn .= ';port=' . $db['port'];
    $dsn .= ';charset=' . $db['charset'];
    $pdo = new PDO($dsn, $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    return $pdo;
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    if (!empty($settings['path'])) {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(0, Monolog\Logger::DEBUG, true, true));
    }
    return $logger;
};

// Mailer
// $container['mailer'] = function ($c) {
//     $settings = $c->get('settings')['mail'];
//     date_default_timezone_set('UTC');

//     $mailer = new PHPMailer();
//     $mailer->setLanguage('pt_br');
//     $mailer->isSMTP();
//     $mailer->isHTML(true);
//     $mailer->SMTPAuth = true;
//     $mailer->SMTPDebug = 0;
//     $mailer->Debugoutput = 'html';
//     $mailer->Host = $settings['host'];
//     $mailer->Port = $settings['port'];
//     $mailer->SMTPSecure = $settings['smtpSecureType'];
//     $mailer->Username = $settings['username'];
//     $mailer->Password = $settings['password'];
//     $mailer->setFrom($settings['username'], 'Âncora EAD');
//     $mailer->SMTPOptions = [
//         'ssl' => [
//             'verify_peer' => false,
//             'verify_peer_name' => false,
//             'allow_self_signed' => true,
//         ],
//     ];
//     if ($c->get('settings')['displayErrorDetails']) {
//         $mailer->SMTPDebug = 3;
//     }

//     return new Farol360\Ancora\Mailer($mailer);
// };

// Parsedown: Markdown parser
// $container['markdown'] = function ($c) {
//     return new Farol360\Ancora\MarkdownParser();
// };

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        // return $c->get('view')->render(
        //     $response->withStatus(404),
        //     '404.twig'
        // );
        return $response->withStatus(404);
    };
};

// $container['version'] = $settings['version'];
$container['ambiente_windows'] = $settings['ambiente_windows'];

// Farol360\Ancora\User::setupUser($container);
// Farol360\Ancora\AdminAncora::setupUser($container);

// ------------------
// ------------------
// Controllers
// ------------------
// ------------------


$container['Farol360\Ancora\Controller\OrderController'] = function ($c) {
    return new Farol360\Ancora\Controller\OrderController(
        // $c['mailer'],
        new Farol360\Ancora\Model\OrderModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory(),
        $c['ambiente_windows']
    );
};

// $container['Farol360\Ancora\Controller\UserController'] = function ($c) {
//     return new Farol360\Ancora\Controller\UserController(
//         $c['view'],
//         $c['flash'],
//         new Farol360\Ancora\Model\UserModel($c['db']),
//         $c['mailer'],
//         new Farol360\Ancora\Model\EntityFactory()
//     );
// };
