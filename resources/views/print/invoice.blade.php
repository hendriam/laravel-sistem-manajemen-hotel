<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @media print {
            body {
                font-family : "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                width: 100%;
            }
        }
        @page  
        { 
            size: auto;
            margin: 2mm 2mm 2mm 2mm;
        } 
        body {
            font-family : "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-family: monospace;
            font-size: 14px;
        }
        #divPrint{
            letter-spacing: -1px;
            line-height: 70%;
        }
        td{
            line-height:10px;
        }
        .title-struk{
            font-size: 18px;
            line-height:14px;
        }
    </style>
</head>
<body>
    <div id="divPrint">
        <?php echo $print; ?>
    </div>
</body>
</html>

<script type="text/javascript">
    window.print();
    self.close();
</script>