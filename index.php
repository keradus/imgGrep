<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "vendor\keradus\Psr4Autoloader\src\Keradus\Psr4Autoloader.php";

$psr4autoloader = new \Keradus\Psr4Autoloader();
$psr4autoloader->register();
$psr4autoloader->addNamespace("Ker", "vendor\keradus\Ker\src");

require_once "res.form.pl.php";

$galleriesDirs = array(
    "./exampleGallery" => "exampleGallery",
);

$form = new \Ker\Form(array(
    "formId" => NULL,
    "formClass" => "form",
    "formAction" => "",
    "formMethod" => "post",
    "formSubmit" => "<div class='centered'><input type='submit' value='Szukaj' class='submit' name='form_search' /></div>",
    "formTrigger" => "form_search",
    "formWithFiles" => true,
    "ulId" => NULL,
    "ulClass" => NULL,
    "errorClass" => "error errorBlock",
    "isXhtml" => true,
    "output" => function(& $_form) use ($galleriesDirs) {
        $fields = $_form->getFields();
        $files = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fields["dir"])) AS $item) {
            if ($item->isFile()) {
                $files[] = $item->getPathname();
            }
        }

        $config = [
            "params" => [
                "gray" => ($fields["gray"] === "Y" ? 1 : 0),
                "resize" => ($fields["resize"] === "Y" ? 1 : 0),
                "limit" => $fields["limit"],
                "identical" => $fields["identical"],
                "file" => $fields["file"]["filepath"],
                "algorithm" => $fields["algorithm"],
            ],
            "galleries" => $galleriesDirs,
        ];

        $ret = "<div id='progressbar'></div>";
        $ret .= "<div id='imgGrep-config' data-config='" . json_encode($config) . "'>";
        $ret .= "<ul id='loadList'><li>" . join("</li><li>", $files) . "</li></ul>";
        $ret .= "<ul id='results' class='textLeft'></ul>";

        return $ret;
    },
));

$form->add(\Ker\FormType::file(), array(
    "name" => "file",
    "label" => "Szukaj obrazka:",
    "check" => array(
        "min" => 1,
        "fileReceived" => true,
        "fileTypes" => implode(" ", \Ker\Graphics\ImageFileLoader::getAvailableTypes()),
        "fun" => function(& $_value) {
            // jesli nie przeslano pliku - nie sprawdzamy
            if (!\Ker\Utils\File::fileWasSent($_value)) {
                return NULL;
            }

            try {
                $img = new \Ker\Graphics\Image(new \Ker\Graphics\ImageFileLoader($_value["tmp_name"]));
                $file = "temp/" . uniqid("img_") . ".gd2";
                $img->saveToGD2($file);
                $_value["filepath"] = $file;
            } catch (\Exception $e) {
                return "Plik uszkodzony!";
            }
        },
    ),
));

$form->add(\Ker\FormType::checkbox(), array(
    "name" => "resize",
    "label" => "Zezwól na dopasowanie rozmiarów:",
    "values" => array(
        "Y" => "tak",
    ),
    "default" => "N",
));

$form->add(\Ker\FormType::checkbox(), array(
    "name" => "gray",
    "label" => "Sprawdzaj w <dfn title='Szukanie w skali szarości jest szybsze, lecz mniej dokładne. Zamiast sprawdzać kanały RGBA sprawdzana jest wyliczona szarość.'>skali szarości</dfn>:",
    "values" => array(
        "Y" => "tak",
    ),
    "default" => "N",
));

$form->add(\Ker\FormType::checkbox(), array(
    "name" => "identical",
    "label" => "Pokaż tylko identyczne:",
    "values" => array(
        "Y" => "tak",
    ),
    "default" => "N",
));

$form->add(\Ker\FormType::text(), array(
    "name" => "limit",
    "label" => "<dfn title='Jeżeli nie podano - wyniki nie będą limitowane.'>Limit wyników</dfn>:",
    "check" => array(
        "fun" => function (& $_value) {
            if ($_value === "") {
                return NULL;
            }

            if (!preg_match("/^[-+]?\\d+$/", $_value)) {
                return "Należy podać liczbę naturalną!";
            }
        }
    ),
));

$algorithms = \Ker\Graphics\Comparator::getAllowedInstanceNames();

$form->add(\Ker\FormType::select(), array(
    "name" => "algorithm",
    "label" => "Algorytm:",
    "values" => array_combine($algorithms, $algorithms),
    "check" => array(
    ),
));

$form->add(\Ker\FormType::radio(), array(
    "name" => "dir",
    "label" => "Wyszukuj w bazie obrazków:",
    "values" => $galleriesDirs,
    "value" => array_keys($galleriesDirs)[0],
    "check" => array("min" => 1),
));
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <title>imgGrep</title>
        <script src="static/3rd/jquery/jquery-2.1.1.min.js"></script>
        <link rel="stylesheet" href="static/3rd/jquery/ui-lightness/jquery-ui-1.10.4.min.css" />
        <script src="static/3rd/jquery/jquery-ui-1.10.4.min.js" type="text/javascript"></script>
        <script src="static/js/jQuery.ajaxPool.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="static/css/Ker_all_v1.css" media="all" />
        <link rel="stylesheet" type="text/css" href="static/css/style.css" media="all" />
        <script src="static/js/imgGrep.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="searchFormContainer" class="w50 centered">
            <h1><a href="/imgGrep">imgGrep</a></h1>
            <?php echo $form->run(["onSuccessReturnOutput" => true, ]); ?>
        </div>
    </body>
</html>