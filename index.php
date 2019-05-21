<?php
include("libs/libs.php");
include("libs/router.php");
include("libs/build_menu.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <title>LogHappens</title>

    <!-- Favicons-->
    <link rel="icon" href="images/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon-precomposed" href="images/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#1bb7a0">
    <meta name="msapplication-TileImage" content="images/favicon/mstile-144x144.png">
    <?php if ($local_static) { ?>
    <link rel="stylesheet" href="static/materialize.min.css">
    <link rel="stylesheet" href="static/css.css?family=Comfortaa">
    <?php } else { ?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Comfortaa">
    <?php } ?>
    <link rel="stylesheet" href="css/custom.css">

    <script src="https://cdn.jsdelivr.net/npm/@iconify/iconify@1.0.0-rc7/dist/iconify.min.js"></script>
</head>
<body data-color-default="<?= $colors['default'] ?>" data-color-notice="<?= $colors['notice'] ?>">
    <header id="header" class="page-topbar">
        <?php include('contents/elements/header.php') ?>
    </header>

    <main id="main">
        <div class="row">
            <aside id="left-sidebar-nav" class="col s12 m12 l3 no-padding">
                <?php include('contents/elements/sidemenu.php') ?>
            </aside>

            <section id="content" class="col s12 m12 l9">
                <div class="log-container">
                    <?php include($page); ?>
                </div>
            </section>
        </div>
    </main>

    <?php include('contents/elements/confirm_truncate.php') ?>

    <?php if ($local_static) { ?>
    <script type="text/javascript" src="static/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="static/materialize.min.js"></script>
    <script type="text/javascript" src="static/push.min.js"></script>
    <?php } else { ?>
    <script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/push.js/0.0.13/push.min.js"></script>
    <?php } ?>
    <script type="text/javascript">
        $(document).ready(function() {
            /**
             * Materialie methods initialization
             */
            $('.button-collapse').sideNav();
            $('.modal').modal();

            /**
             * Select box
             */
            $('select').material_select();
            $('.page-length').on('change', function() {
                var length = $(this).val();
                var url = 'ajax.php?token=<?= $token ?>&action=set_pagelength&length=' + length;
                $.ajax({
                    method: 'GET',
                    url: url
                })
                .done(function() {
                    window.location.reload();
                });
            });

            /**
             * Live reloading instructions
             */
            var currentUrl = '?' + window.location.search.substring(1);
            $('.side-nav a[data-tracked=true]').each(function() {
                var $link = $(this);
                var link = $link.attr('href');

                // Recount logs every X seconds
                setInterval(function() {
                    recount(link, true);
                }, <?php echo $GLOBALS['interval']; ?>);
            });

            /**
             * Recount logs and trigger warnings when something happens
             *
             * @param {string} link The url of the log's page, for refreshing logs
             * @param {bool} push Show push messages or not
             */
            function recount(link, push) {
                var $link = $('.side-nav a[href="' + link + '"]');
                var $badge = $link.find('.badge');
                var howMany = $link.attr('data-howmany');
                var action = link.replace('log_reader', 'log_counter');

                $.ajax({
                    url: 'ajax.php' + action
                }).done(function(howManyNew) {
                    if (howManyNew !== howMany) {
                        var difference = Number(howManyNew) - Number(howMany);
                        if (push === true) {

                            Push.create('LogHappens!', {
                                body: $link.attr('data-name') + ': ' + difference + ' new logs!',
                                icon: {
                                    x16: 'images/logo.png',
                                    x32: 'images/logo.png'
                                },
                                link: $link.attr('href'),
                                timeout: 5000
                            });

                            if (currentUrl === link) {
                                reloadContent(link);
                            }
                            $badge.addClass('new');
                        }

                        $badge.html("");
                        $link.attr('data-howmany', howManyNew);
                    } else {
                        howMany = $link.attr('data-howmany');
                        $badge.html(howMany);
                        $badge.removeClass('new');
                    }
                });
            }

            /**
             * Refresh the log's list
             *
             * @param {string} link The url of the log's page, for refreshing logs
             */
            function reloadContent(link) {
                var $link = $('.side-nav a[href="' + link + '"]');
                var colorDefault = $('body').attr('data-color-default');
                var colorNotice = $('body').attr('data-color-notice');

                $('.log-container').load('ajax.php' + link);
                $('.color-themed').addClass(colorNotice).removeClass(colorDefault);

                setTimeout(function() {
                    $('.color-themed').addClass(colorDefault).removeClass(colorNotice);
                }, 3000);
            }

            /**
            * TruncateLink method
            *
            */
            $('.truncateLink').on('click', function(e) {
                e.preventDefault();
                var link = $(this).attr('href');
                var $modal = $("#confirm_truncate");
                $modal.modal('open');

                $modal.find('.yes-btn').click(function() {
                    window.location.href = link;
                });
                $modal.find('.no-btn').click(function() {
                    $('#confirm_truncate').modal('close');
                });
            });

            /**
            * ViewLink method
            *
            */
            $('.viewLink').on('click', function(e) {
                e.preventDefault();

                // This is needed to copy the log's path into the clipboard
                var $temp = $('<input>');
                var link = $(this).attr('href');
                $('body').append($temp);
                $temp.val(link).select();
                document.execCommand('copy');
                $temp.remove();

                // Then show a toast to the user
                Materialize.toast('This log\'s path has been copied to your clipboard. Please paste it into a new tab to see the log file.', 5000, 'indigo darken-3');
            });

        });
    </script>
</body>
</html>
