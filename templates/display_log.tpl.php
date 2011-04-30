<!DOCTYPE html>
<html>
    <head>
    <title>w-Slow, Log and Find Slow Queries</title>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta name="ROBOTS" content="NOINDEX,NOFOLLOW">
	<link rel="stylesheet" href="styles/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="styles/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]>
        <link rel="stylesheet" href="styles/blueprint/ie.css" type="text/css" media="screen, projection">
    <![endif]-->
	<link rel="stylesheet" href="../../blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">
    <link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'>
    <style>
        header { font-family: 'Reenie Beanie', arial, serif; }
        caption { background: #fff; font-weight: bold; }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/webfont/1.0.4/webfont.js"></script>
    <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    </head>
    <body>
        <section class="container">
            <header>
                <h1 class="logo span-2">w-slow</h1>
		        <hr>
		        <h2 class="alt">Log and find slow queries.</h2>
		        <hr>
		    </header>
		    <section>
		        <div id="result"></div>
		        <section id="slow_queries">
		        <?php if($sl){ ?>

		                <?php foreach ($sl as $s){
		                echo "<p><strong>Query:</strong> {$s['query']}</p>";
		                echo "<p><strong>Rows Sent:</strong> {$s['rows_sent']}</p>";
		                echo "<p><strong>Rows Examined:</strong> {$s['rows_examined']}</p>";
		                echo '<table>'
		                    .'<caption><a href="http://dev.mysql.com/doc/refman/5.0/en/using-explain.html" title="documentation for EXPLAIN">Query Execution Plan</a></caption>'
		                    .'<thead>'
		                    .'<tr class="rounded">'
		                    .'<th>Select Type</th>'
		                    .'<th>Table</th>'
		                    .'<th>Type</th>'
		                    .'<th>Possible Keys</th>'
		                    .'<th>Key Used</th>'
		                    .'<th>Key Size</th>'
		                    .'<th>Ref</th>'
		                    .'<th>Rows</th>'
		                    .'<th>Extra</th>'
		                    .'</tr>'
		                    .'</thead>'
		                    .'<tbody>';
                            foreach ($s['explain'] as $explain) {
                                echo '<tr>'
                                    ."<td>{$explain['select_type']}</td>"
                                    ."<td>{$explain['table']}</td>"
                                    ."<td>{$explain['type']}</td>";
                                    if ( ($explain['type'] == 'index') OR ($explain['type'] == 'ALL') ){
                                        $explain_type_bad = true;
                                    } else {
                                        if (!isset($explain_type_bad)){
                                            $explain_type_bad = false;
                                        }
                                    }
                                echo "<td>{$explain['possible_keys']}</td>"
                                    ."<td>{$explain['key']}</td>"
                                    ."<td>{$explain['key_len']}</td>"
                                    ."<td>{$explain['ref']}</td>"
                                    ."<td>{$explain['rows']}</td>"
                                    ."<td>{$explain['extra']}</td>"
                                    .'</tr>';
                            }
		                    echo '</tbody>'
		                    .'</table>';
		                    if ($explain_type_bad){
		                        echo "<p><strong>Tip:</strong> This query has a type of INDEX or ALL meaning that it is reading the entire index, or the entire table in order to get a result, which is rather bad.  Check your indexes on this table against any JOIN value reference or WHERE reference";
		                        $explain_type_bad = false;
		                    }

                            echo '<hr>';

		                } ?>

		        <?php } else {
		                    echo 'Slow Log is Empty!';
		                    $empty_log = true;
		                    } ?>

                </section>
                <?php
                if (!isset($empty_log)){
                ?>
                <p>
                    <a id="truncate" href="#result">
                        Truncate and Turn OFF Slow Query Log
                    </a>
                </p>
                <?php } ?>

		    </section>
            <hr class="space">
            <hr>
        </section>
            <script>
                $(document).ready(function() {
                    $('#truncate').click(function() {
                        var loadUrl = 'index.php?&phase=truncate_log';
                        $("#result").fadeIn();
                        $("#result").html(ajax_load);
                        $.get(
                            loadUrl,
                            {language: "php", version: 5},
                            function(responseText){
                                $("#result").html(responseText);
                                $("#slow_queries").fadeOut();
                            },
                            "html"
                        );
                        $("#truncate").fadeOut();
                        window.location.hash = '#result';
                    });
                });
            </script>
     </body>
</html>