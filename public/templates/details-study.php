<?php if (!defined('ABSPATH')) exit; ?>


<div class="details-study-fr" style="display: none;">
    <?php
    include('partials/details-study-data-fr.php');
    ?>
</div>

<div class="details-study-en" style="display: none;">
    <?php
    include('partials/details-study-data-en.php');

    ?>
</div>

<div class="detail-container form-nada-add">
    <div id="stepper">
        <ul class="nav nav-pills m-0 nav nav-pills m-0 d-flex justify-content-end">
            <li class="nav-item langue">
                <a class="nav-link active" data-lang="fr" href="#">FR</a>
            </li>
            <li class="nav-item langue">
                <a class="nav-link" data-lang="en" href="#">EN</a>
            </li>
        </ul>
        <!-- Step indicators -->
        <ul class="nav nav-tabs" id="stepperTabs">
            <button class="nav-link active" id="step-1-tab" data-bs-toggle="tab" data-bs-target="#detaill-1" type="button" role="tab" aria-controls="detaill-1" aria-selected="true">Renseignements sur le contexte de la collecte des données</button>
            <button class="nav-link" id="step-2-tab" data-bs-toggle="tab" data-bs-target="#detaill-2" type="button" role="tab" aria-controls="detaill-2" aria-selected="true">Methodologie de l'étude</button>
            <button class="nav-link" id="step-3-tab" data-bs-toggle="tab" data-bs-target="#detaill-3" type="button" role="tab" aria-controls="detaill-3" aria-selected="true">Caractéristique données collectées</button>
        </ul>

        <!-- Step contents -->
        <div class="tab-content" id="stepperContent" style="margin-bottom: 40px;">

            <?php
            include('detail-step-1.php');
            ?>
            <?php
            include('detail-step-2.php');
            ?>
            <?php
            include('detail-step-3.php');
            ?>

        </div>
    </div>
</div>