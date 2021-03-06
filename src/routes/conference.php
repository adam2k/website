<?php
return function(\Slim\Slim $app, array $options) {
    $app->get('/', function() use($app, $options) {
        $app->render('home.html', ['page' => 'home'] + $options);
    })->name("{$options['year']}-home");

    if (isset($options['callForPapers'])) {
        $app->get('/cfp', function() use($app, $options) {
            $app->render('cfp.html', ['page' => 'topics'] + $options);
        })->name("{$options['year']}-cfp");

        $app->post('/cfp', function() use($app, $options) {
            $req = $app->request();
            try {
                $packet = [
                    'name' => $req->post('name'),
                    'email' => $req->post('email'),
                    'summary' => $req->post('summary'),
                    'type' => $req->post('type'),
                ];

                //adding json so i can easily cut an paste into our "database"
                $json = json_encode($packet);

                $app->mailgun->sendMessage(
                    $app->mailgunDomain,
                    [
                        'from' => "{$req->post('name')} <{$req->post('email')}>",
                        'to' => $app->cfpEmail,
                        'subject' => 'Call For Papers',
                        'text' => "{$req->post('type')}\n\n{$req->post('summary')}\n\n{$json}",
                    ]
                );

                $app->flashNow('success', 'Your proposal has been received.  Thanks for submitting!');
            } catch (\Exception $e) {
                $app->flashNow('error', 'There was an error saving your proposal.  Please verify your information and try again.');
            }

            $app->render('cfp.html', ['page' => 'cfp'] + $options);
        })->name("{$options['year']}-cfp-submit");
    }

    if (isset($options['topics'])) {
        $app->get('/topics', function() use($app, $options) {
            $app->render('topics.html', ['page' => 'topics'] + $options);
        })->name("{$options['year']}-topics");
    }

    if (isset($options['people'])) {
        $app->get('/people', function() use($app, $options) {
            ksort($options['people']);
            $app->render('people.html', ['page' => 'people'] + $options);
        })->name("{$options['year']}-people");
    }

    if (isset($options['schedule'])) {
        $app->get('/schedule', function() use($app, $options) {
            $app->render('schedule.html', ['page' => 'schedule'] + $options);
        })->name("{$options['year']}-schedule");
    }

    if (isset($options['faq'])) {
        $app->get('/faq', function() use($app, $options) {
            $app->render('faq.html', ['page' => 'faq'] + $options);
        })->name("{$options['year']}-faq");
    }

    if (isset($options['survey'])) {
        $app->get('/survey', function() use($app, $options) {
            $app->redirect($options['survey']);
        })->name("{$options['year']}-survey");
    }

    if (isset($options['uncon'])) {
        $options['logo'] = 'http://cdn0.traderonline.com/v1/media/de-dev-un-con.png';
        $app->get('/uncon', function() use($app, $options) {
            $app->render('uncon.html', ['page' => 'uncon'] + $options);
        })->name("{$options['year']}-uncon");
    }
};
