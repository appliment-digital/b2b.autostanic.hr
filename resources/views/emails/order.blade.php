<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />

    <style>
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
            background: #edf2ff;
        }

        #order {
            border-collapse: collapse;
        }

        #order tr{
            border-bottom: 0.5px solid rgb(201, 194, 194);
        }
    </style>

</head>

<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

    <table data-bgcolor="BodyBg" data-module="01-Top Space Part" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#edf2ff" align="center" class="">
        <tbody>
            <tr>
                <td valign="top" align="center">
                    <table class="main" width="800" cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table data-bgcolor="BodyBg" data-module="10-Service Title Part" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#edf2ff" align="center" class="">
        <tbody>
            <tr>
                <td valign="top" align="center">
                    <table class="main" width="800" cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td valign="top" bgcolor="#FFFFFF" align="center">
                                    <table class="two-left-inner" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                                <td valign="top" height="50" align="center">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" align="center">
                                                    <table class="two-left-inner" width="100%" cellspacing="0" cellpadding="0" border="0">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <table width="170" cellspacing="0" cellpadding="0" border="0" align="center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><a href="#" style="text-decoration:none;"><img src="{{ asset('images/as-logo.png') }}" style="width: 200px;" /></a>
                                                                                    </multiline>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#666666; font-weight:bold; text-transform:uppercase;" mc:edit="fm-39" valign="top" align="center" height="40">
                                                                    <multiline></multiline>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-family:'Open Sans', sans-serif, Verdana; font-size:20px; color:#203c4d; font-weight:bold; line-height:50px; text-transform:none;" mc:edit="fm-40" valign="top" align="center">
                                                                    <multiline>{{ $title }}</multiline>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size:5px; line-height:5px;" valign="top" height="10" align="center">&nbsp;
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#3b3a3a; font-weight:normal; line-height:24px;" mc:edit="fm-41" valign="top" align="left">
                                                                    <multiline>Poštovani {{ $full_name }}, narudžba je uspješno poslana.</multiline>
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td style="font-size:5px; line-height:20px;" valign="top" height="15" align="center">&nbsp;
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <table id="buyerData" width="100%" cellspacing="1" cellpadding="0" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#203c4d; font-weight:normal; line-height:24px;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td><span style="font-weight:bold;">Adresa dostave: </span>{{ $address }}, {{ $postal_code }} {{ $city }}, {{ $country_name }}</td>
                                                                            </tr>
                                                                            @if($note !='')         
                                                                                <td><span style="font-weight:bold;">Napomena: </span>{{ $note }}</td>      
                                                                            @endif
                                                                            <tr>
                                                                                <td><span style="font-weight:bold; font-size:16px;">Dostava je besplatna.</span></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td style="font-size:5px; line-height:20px;" valign="top" height="15" align="center">&nbsp;
                                                                </td>
                                                            </tr>

                                                            <!-- Table for items -->
                                                            <tr>
                                                                <td>
                                                                    <table id="order" width="100%" cellspacing="1" cellpadding="1" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#3b3a3a; font-weight:normal; line-height:24px;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="2" style="color: #203c4db3;">Artikl</th>
                                                                                <th style="width: 10%; text-align: center; color: #203c4db3;">Kol</th>
                                                                                <th style="color: #203c4db3;">Cijena</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($order_items as $item)
                                                                            <tr>
                                                                                <td><img src="{{ $item['picture'] }}" style="width: 50px; height: auto;"></td>
                                                                                <td>{{ $item['name'] }}</td>
                                                                                <td style="width: 10%; text-align: center;">{{ $item['quantity'] }}</td>
                                                                                <td style="text-align: center;">{{ $item['priceString'] }} €</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr style="font-weight: 600;">
                                                                                <td colspan="3" align="right">Ukupno: </td>
                                                                                <td style="width: 20%; text-align: center;">{{ $order_total }} €</td>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td style="font-size:5px; line-height:5px;" valign="top" height="15" align="center">&nbsp;
                                                                </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td>
                                                                    <table style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#3b3a3a; font-weight:normal; line-height:24px;">
                                                                        <tr>
                                                                            <td>Srdačan pozdrav, <br> Auto Stanić</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size:5px; line-height:5px;" valign="top" height="15" align="center">
                                                                    &nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" align="center">&nbsp;</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:80px; line-height:80px;" valign="top" height="80" align="center">&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>