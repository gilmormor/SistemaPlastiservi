<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        p, label, span, table{
            font-family: 'BrixSansRegular';
            font-size: 9pt;
        }
        .h2{
            font-family: 'BrixSansBlack';
            font-size: 16pt;
        }
        .h3{
            font-family: 'BrixSansBlack';
            font-size: 12pt;
            display: block;
            background: rgb(0, 119, 255);
            color: #FFF;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
        }
        #page_pdf{
            width: 95%;
            margin: 15px auto 10px auto;
        }

        #factura_head, #factura_cliente, #factura_detalle{
            width: 100%;
            margin-bottom: 10px;
        }
        .logo_factura{
            width: 20%;
        }
        .info_empresa{
            width: 40%;
            text-align: center;
        }
        .info_factura{
            width: 40%;
        }
        .info_cliente{
            width: 100%;
        }
        .datos_cliente{
            width: 100%;
        }
        .datos_cliente tr td{
            width: 50%;
        }
        .datos_cliente{
            padding: 10px 10px 0 10px;
        }
        .datos_cliente label{
            width: 75px;
            display: inline-block;
        }
        .datos_cliente p{
            display: inline-block;
        }

        .textright{
            text-align: right;
        }
        .textleft{
            text-align: left;
        }
        .textcenter{
            text-align: center;
        }
        .round{
            border-radius: 10px;
            border: 1px solid #0a4661;
            overflow: hidden;
            padding-bottom: 15px;
        }
        .round p{
            padding: 0 15px;
        }
        .round1{
            width:40% !important;
            border-radius: 10px;
            border: 1px solid #0a4661;
            overflow: hidden;
            padding-bottom: 15px;
        }
        .round2{
            width:100% !important;
            border-radius: 10px;
            border: 1px solid #0a4661;
            overflow: hidden;
            padding-bottom: 15px;
        }

        #factura_detalle{
            border-collapse: collapse;
        }
        #factura_detalle thead th{
            background: rgb(0, 119, 255);
            color: #FFF;
            padding: 5px;
        }
        #detalle_productos tr:nth-child(even) {
            background: #ededed;
        }
        #detalle_totales span{
            font-family: 'BrixSansBlack';
        }
        .nota{
            font-size: 8pt;
        }
        .label_gracias{
            font-family: verdana;
            font-weight: bold;
            font-style: italic;
            text-align: center;
            margin-top: 20px;
        }
        .anulada{
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateX(-50%) translateY(-50%);
        }

        .headt td {
            height: 20px;
        }

        H1.SaltoDePagina
        {
            PAGE-BREAK-AFTER: always
        }

        .estrella {
            margin: 50px 0;
            position: relative;
            display: block;
            color: red;
            width: 0px;
            height: 0px;
            border-right: 100px solid transparent;
            border-bottom: 70px solid red;
            border-left: 100px solid transparent;
            transform: rotate(35deg);
        }

        .estrella:before {
            border-bottom: 80px solid red;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            position: absolute;
            height: 0;
            width: 0;
            top: -45px;
            left: -65px;
            display: block;
            content: '';
            transform: rotate(-35deg);
        }

        .estrella:after {
            position: absolute;
            display: block;
            color: red;
            top: 3px;
            left: -105px;
            width: 0px;
            height: 0px;
            border-right: 100px solid transparent;
            border-bottom: 70px solid red;
            border-left: 100px solid transparent;
            transform: rotate(-70deg);
            content: '';
        }

        .cuadrado-2 {
            width: 10px; 
            height: 10px; 
            border: 1px solid #555;
        }

        .cuadrado-3 {
            width: 10px; 
            height: 10px; 
            border: 1px solid #555;
            background: #ffffff;
        }

        .circuloborde {
            width: 10px; 
            height: 10px; 
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            border: 1px solid rgb(0, 0, 0);
            background: #ffffff;
        }

        .circulo {
            width: 10px; 
            height: 10px; 
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            border: 1px solid rgb(0, 0, 0);
            background: #000000;
        }

        .star-five {
            margin: 50px 0;
            position: relative;
            display: block;
            color: red;
            width: 0px;
            height: 0px;
            border-right: 100px solid transparent;
            border-bottom: 70px solid red;
            border-left: 100px solid transparent;
            transform: rotate(35deg);
        }
        .star-five:before {
            border-bottom: 80px solid red;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            position: absolute;
            height: 0;
            width: 0;
            top: -45px;
            left: -65px;
            display: block;
            content: '';
            transform: rotate(-35deg);
        }
        .star-five:after {
            position: absolute;
            display: block;
            color: red;
            top: 3px;
            left: -105px;
            width: 0px;
            height: 0px;
            border-right: 100px solid transparent;
            border-bottom: 70px solid red;
            border-left: 100px solid transparent;
            transform: rotate(-70deg);
            content: '';
        }

        .width10{
            width: 10px !important;
        }
        .width20{
            width: 20px !important;
        }
        .width30{
            width: 30px !important;
        }
        .width40{
            width: 40px !important;
        }
        .width50{
            width: 50px !important;
        }
        .width60{
            width: 60px !important;
        }
        .width70{
            width: 70px !important;
        }
        .width80{
            width: 80px !important;
        }
        .width90{
            width: 90px !important;
        }
        .width100{
            width: 100px !important;
        }
        .width200{
            width: 200px !important;
        }

        .headtarial td {
            height: 20px;
        }

        .headtarial td p {
            font-size: 13px;
            font-family: Arial;
        }

        #reporte_detalle thead th{
            background: rgb(255, 255, 255);
            color: #000000;
            padding: 2px;
        }
        #reporte_detalle{
            width: 100%;
            margin-bottom: 5px;
        }
        #reporte_detalle{
            border-collapse: collapse;
        }

        .small-text {
            font-size: smaller;
            color: #444; /* Color que no sea tan negro */
        }
    </style>
    <title>Inicio de despacho</title>
</head>
<body>

 
    <div style='width:40% !important;'>
        <span class='h3'>Datos usuario que generó el correo:</span>
        <table id='info_factura'>
            <tr>
                <td colspan='7' class='textleft' width='40%'><span><strong>Nombre: </strong></span></td>
                <td class='textleft' width='50%'><span>{{session()->get('nombre_usuario') }}</span></td>
            </tr>
            <tr>
                <td colspan='7' class='textleft' width='40%'><span><strong>Email: </strong></span></td>
                <td class='textleft' width='50%'><span>{{Auth::user()->email}}</span></td>
            </tr>
        </table>
    </div>
    
    <?php
        echo $cuerpo;
    ?>

    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="https://www.pl.plastiservi.cl">
            https://www.pl.plastiservi.cl
        </a>
    </p>
</body>
</html>