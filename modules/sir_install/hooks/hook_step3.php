<?php
/*
 *  IdPRef - IdP de Referencia para SIR 2 basado en SimpleSAMLPHP v1.13.1
 * =========================================================================== *
 *
 * Copyright (C) 2014 - 2015 by the Spanish Research and Academic Network.
 * This code was developed by Auditoria y Consultoría de Privacidad y Seguridad
 * (PRiSE http://www.prise.es) for the RedIRIS SIR service (SIR: 
 * http://www.rediris.es/sir)
 *
 * *****************************************************************************
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * ************************************************************************** */

/** 
 * Paso 3 del modulo instalador para SimpleSAMLphp v1.13.1
 * @package    IdPRef\modules\sir_install
 * @author     "PRiSE [Auditoria y Consultoria de privacidad y Seguridad, S.L.]"
 * @copyright  Copyright (C) 2014 - 2015 by the Spanish Research and Academic
 *             Network
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version    IdPRef-Sprint2
 */

/**
 * Hook a ejecutar antes del paso 3 de la instalación
 * 1) Define la contraseña de administrador (se debe preguntar al usuario)
 * 2) Define el grano de sal (automáticamente)
 * 3) Define información sobre el contacto técnico(se debe preguntar al usuario)
 * 4) Define el idioma por defecto de Castellano(automáticamente)
 * 5) Define la zona horaria por defectosegún el PHP.ini(automáticamente, pero se informa al usuario)
 *
 * @param array &$data  Los datos a utilizar por las plantillas de tipo stepn
 */
function sir_install_hook_step3(&$data) {
    if (array_key_exists('ssphp_password', $_REQUEST) && array_key_exists('ssphp_password2', $_REQUEST) && !empty($_REQUEST['ssphp_password'])) {
        $pass  = $_REQUEST['ssphp_password'];
        $pass2 = $_REQUEST['ssphp_password2'];
        if (strcmp($pass, $pass2) == 0) {
            if (array_key_exists('ssphp_technicalcontact_name', $_REQUEST) && array_key_exists('ssphp_technicalcontact_email', $_REQUEST) && !empty($_REQUEST['ssphp_technicalcontact_name']) && !empty($_REQUEST['ssphp_technicalcontact_email'])) {
                $filename                         = __DIR__ . '/../../../config/config.php';
                include($filename);
                $config['auth.adminpassword']     = $pass;
                $config['secretsalt']             = bin2hex(openssl_random_pseudo_bytes(16));
                $config['technicalcontact_name']  = $_REQUEST['ssphp_technicalcontact_name'];
                $config['technicalcontact_email'] = $_REQUEST['ssphp_technicalcontact_email'];
                $config['language.default']       = "es";
                $config['timezone']               = date_default_timezone_get();
                $config['timezone']               = date_default_timezone_get();
                $config['enable.saml20-idp']      = true;
                $config['enable.shib13-idp']      = false;
                $config['enable.adfs-idp']        = false;
                $config['enable.wsfed-sp']        = false;
                $res                              = @file_put_contents($filename, '<?php  $config = ' . var_export($config, 1) . "; ?>");
                if (!$res) {
                    $data['errors'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_contact_save_error}');
                    $data['errors'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_contact_save_error2}') . " <i>" . realpath($filename) . "</i>";
                } else {
                    $data['warning'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_timezone_info}') . " <i>" . $config['timezone'] . "</i>. " . $data['ssphpobj']->t('{sir_install:sir_install:step2_timezone_info2}');
                }
            } else {
                $data['errors'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_contact_info_error}');
            }
        } else {
            $data['errors'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_passwords_error}');
        }
    } else {
        $data['errors'][] = $data['ssphpobj']->t('{sir_install:sir_install:step2_password_ko_error}');
    }
    return true;
}
