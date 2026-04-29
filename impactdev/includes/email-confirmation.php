<?php
return   '
       <!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Email FReSH</title>
  <style type="text/css">
    /* Reset basique */
    body,
    table,
    td,
    a {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
      display: block;
      outline: none;
      text-decoration: none;
      height: auto;
    }

    body {
      margin: 0;
      padding: 0;
      width: 100% !important;
      background: #ffffff;
      font-family: Arial, Helvetica, sans-serif;
      color: #333333;
      text-align: left;
    }

    /* Conteneur principal fluide */
    .email-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      text-align: left;
    }

    /* Colonnes responsive : par défaut côte-à-côte sur desktop */
    .two-col {
      width: 100%;
    }

    .col {
      vertical-align: top;
    }

    /* FOR MOBILE: transformer les colonnes en block (stack) */
    @media screen and (max-width:480px) {
      .stack {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
      }

      .stack-center {
        text-align: center !important;
      }

      .footer-logo {
        border-right: 0 !important;
        border-bottom: 1px solid #00a6e2 !important;
        padding-bottom: 12px !important;
        margin-bottom: 8px !important;
      }

      .footer-text {
        padding-top: 12px !important;
      }

      .no-pad-mobile {
        padding: 0 !important;
      }
    }

    /* Paragraphes */
    p {
      margin: 0 0 12px 0;
    }
  </style>
</head>

<body style="margin:0;padding:0;">

  <!-- Wrapper -->
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
      <td align="left" style="padding:20px 10px;">

        <!-- Main container -->
        <table align="left" role="presentation" cellpadding="0" cellspacing="0" border="0" class="email-container"
          style="width:100%;max-width:600px; solid #e6e6e6;border-radius:6px;overflow:hidden;">

          <!-- Content (exemple texte principal) -->
          <tr>
            <td style="font-size:15px;line-height:1.6;color:#333333;text-align:left;">
              <p>Bonjour [prenom] [nom],</p>
              <p>Merci pour la création de votre compte sur le <a href="[home_url]">portail FReSH</a> </p>
              <p> Nous sommes ravis de vous compter parmi nos utilisateurs. </p>
			  <p>En vous connectant vous pourrez :</p>
              <ul>
                <li>Proposer vos actualités (articles, événements, offres d’emploi) en lien avec la recherche en santé</li>
                <li>Demander l’accès aux données des études que vous avez découvert dans le catalogue</li>
                <li>Contribuer au catalogue en documentant une de vos études</li>
              </ul>
				  <p>Pour ne manquer aucune de nos actualités, suivez-nous sur <a href="' . esc_url(get_option('impactdev_linkedin', '')) . '" style="text-decoration:underline;">LinkedIn</a>

             
			   <p>Encore merci pour votre confiance !  </p>
               
              <hr style="margin:30px 0; border:none; border-top:1px solid #ddd;">
              </p>
            </td>
          </tr>


          <tr>
            <td style="font-size:15px;line-height:1.6;color:#333333;text-align:left;">
             <p>Hello [prenom] [nom],</p>
              <p>Thank you for creating your account on the FReSH <a href="[home_url]">portal</a>
              </p>
			  <p>We’re delighted to have you among our users. </p>
              <p>By logging in, you’ll be able to:</p>
              <ul>
                <li>Share your news (articles, events, job offers) related to health research</li>
                <li>Request access to data from studies you have found in the catalogue</li>
                <li>Contribute to the catalog by documenting one of your own studies</li>
              </ul>
				  <p>To stay up to date with our latest news, follow us on <a             href="https://www.linkedin.com/company/107055204/admin/dashboard/" style="text-decoration:underline;">LinkedIn</a>
             
			
     
              <p>Thank you again for your trust!</p>
           

            </td>
          </tr>


          <!-- Separator -->
          <tr>
            <td style="line-height:1px;font-size:1px;background:#00a6e2;height:1px;">&nbsp;</td>
          </tr>

          <!-- Footer two-column -->
          <tr>
            <td style="padding:18px 0;background:#ffffff;">

              <!-- Outlook fallback -->
              <!--[if mso]>
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td width="200" valign="top">
              <![endif]-->

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" class="two-col"
                style="border-collapse:collapse; text-align:left;">
                <tr>
                  <!-- Colonne logo -->
                  <td class="col stack footer-logo"
                    style="width:25%; padding-right:12px; border-right:1px solid #00a6e2; vertical-align:top;">
                    <a href="[home_url]" target="_blank" style="text-decoration:none;">
                    <img src="[home_url]/wp-content/uploads/2025/10/Logo_FReSH-1.png" alt="Logo FReSH" style="display:block; max-width:100px;       height:auto;" width="100%">
                  </a>
                  </td>

                  <!-- Colonne texte -->
                  <td class="col stack footer-text" style="width:75%; padding-left:12px; vertical-align:top;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td style="font-size:14px; line-height:20px; color:#333333; text-align:left;">
                          <strong>
                            <p>Équipe FReSH</p>
                          </strong>
                          <p>Portail des études en santé</p>
                          <a href="[home_url]"
                            style="color:#00a6e2; text-decoration:none;">portail-fresh.fr</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!--[if mso]>
                  </td>
                </tr>
              </table>
              <![endif]-->

            </td>
          </tr>

        </table>
        <!-- End main container -->

      </td>
    </tr>
  </table>

</body>

</html>
    ';
