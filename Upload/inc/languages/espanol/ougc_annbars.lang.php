<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/languages/english/ougc_annbars.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 - 2016 Omar Gonzalez
 *
 *    Website: http://omarg.me
 *
 *    Manage announcement notification bars with multiple display rules.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ****************************************************************************/

$l = [
    'ougcAnnouncementBars' => 'ougc Announcement Bars',

    'ougcAnnouncementBarsModeratorControlPanel' => 'Barras de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelNavigation' => 'Barras de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelBreadcrumb' => 'Barras de Anuncios',

    'ougcAnnouncementBarsModeratorControlPanelTableTitle' => 'Barras de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderName' => 'Nombre',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroups' => 'Grupos de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForums' => 'Foros de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScripts' => 'Scripts de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptions' => 'Opciones',

    'ougcAnnouncementBarsModeratorControlPanelEmpty' => 'Actualmente no hay barras de anuncios para mostrar.',

    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsAll' => 'Todos los Grupos',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsNone' => 'Ninguno',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsAll' => 'Todos los Foros',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsNone' => 'Ninguno',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScriptsAll' => 'Todos los Scripts',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScriptsNone' => 'Ninguno',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsEdit' => 'Editar',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsDelete' => 'Eliminar',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsDeleteConfirm' => '¿Estás seguro de que deseas eliminar esta barra de anuncios? Esto no se puede revertir.',

    'ougcAnnouncementBarsModeratorControlPanelButtonNew' => 'Nuevo Anuncio',

    'ougcAnnouncementBarsModeratorControlPanelRedirectNew' => 'La barra de anuncios se agregó correctamente.',
    'ougcAnnouncementBarsModeratorControlPanelRedirectEdit' => 'La barra de anuncios se editó correctamente.',
    'ougcAnnouncementBarsModeratorControlPanelRedirectDelete' => 'La barra de anuncios se eliminó correctamente.',

    'ougcAnnouncementBarsModeratorControlPanelLogActionDelete' => 'Eliminación de Barra de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelLogInformation' => 'Identificador del Anuncio: {1}, Nombre del Anuncio: {2}',

    'ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleNew' => 'Nueva Barra de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleEdit' => 'Editar Barra de Anuncios',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderName' => 'Nombre',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderNameDescription' => 'Selecciona un nombre para identificar esta barra de anuncios en el panel de control del moderador.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderMessage' => 'Mensaje',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderMessageDescription' => 'El mensaje de la barra de anuncios que se mostrará a los usuarios.<pre>
{username} = Nombre de usuario del usuario actual
{forum_name} = Nombre del foro
{forum_url} = URL del foro
{start_date} = Fecha de inicio
{end_date} = Fecha de finalización
{displayKey?} = Para un valor de resultado de una regla de visualización
</pre>',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClass' => 'Clase de Estilo',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassDescription' => 'La clase de estilo CSS de la barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStylePredefined' => 'Predefinido',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBlack' => 'Negro',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassWhite' => 'Blanco',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassRed' => 'Rojo',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassGreen' => 'Verde',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBlue' => 'Azul',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBrown' => 'Marrón',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassPink' => 'Rosa',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassOrange' => 'Naranja',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassCustom' => 'Personalizado',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroups' => 'Grupos de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsDescription' => 'Selecciona los grupos de usuarios que verán esta barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsAll' => 'Todos los Grupos',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsCustom' => 'Grupos Seleccionados',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsNone' => 'Ninguno',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForums' => 'Foros de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsDescription' => 'Selecciona los foros en los que se mostrará esta barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsAll' => 'Todos los Foros',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsCustom' => 'Foros Seleccionados',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsNone' => 'Ninguno',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScripts' => 'Scripts de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScriptsDescription' => 'Selecciona los scripts en los que se mostrará esta barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScriptsCustomPlaceholder' => 'Un script por línea',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStartDate' => 'Fecha de Inicio',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStartDateDescription' => 'Selecciona una fecha de inicio a partir de la cual esta barra de anuncios comenzará a mostrarse.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEndDate' => 'Fecha de Finalización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEndDateDescription' => 'Selecciona una fecha de inicio a partir de la cual esta barra de anuncios dejará de mostrarse.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayRules' => 'Reglas de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayRulesDescription' => 'Una lista de condicionales en formato JSON para manipular la visualización de este anuncio. Consulta el <a href="https://github.com/OUGC-Network/ougc-Announcement-Bars?tab=readme-ov-file#usage">README en el repositorio</a> para más información.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayOrder' => 'Orden de Visualización',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayOrderDescription' => 'Selecciona un orden de visualización para esta barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderAllowDismissal' => 'Permitir Descartar',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderAllowDismissalDescription' => 'Permitir a los usuarios descartar temporalmente esta barra de anuncios.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEnabled' => 'Habilitado',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEnabledDescription' => 'Habilitar esta barra de anuncios.',

    'ougcAnnouncementBarsModeratorControlPanelNewEditButtonNew' => 'Agregar Anuncio',
    'ougcAnnouncementBarsModeratorControlPanelNewEditButtonEdit' => 'Editar Anuncio',

    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidName' => 'El nombre de la barra de anuncios debe tener entre 1 y 100 caracteres.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidMessage' => 'El mensaje de la barra de anuncios no es válido.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidDisplayRules' => 'El código de las reglas de visualización no es válido. Asegúrate de que el código sea compatible con JSON.',

    // Puedes cambiar el mensaje de las barras por el ID del anuncio, por ejemplo, si descomentas la siguiente línea, se mostrará como el mensaje de la barra cuyo ID es 5.
    'ougcAnnouncementBarsCustomBarMessage5' => '<strong>Título:</strong> Hola {username}, estás visitando [url={forum_url}]{forum_name}[/url].',
    'ougcAnnouncementBarsCustomBarMessageX' => '<strong>{username}!!</strong> Haz clic [u][url={forum_url}]aquí[/url][/u] para ser el primero en hacer clic.',

    'ougc_announcement_bars_task_ran' => 'La tarea de barras de anuncios se ejecutó correctamente.',
];