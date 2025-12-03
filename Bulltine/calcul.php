<?php

// =================================================================
// 1. DÉFINITION DES FONCTIONS DE CALCUL DE L'IR (Reste en tête pour la correction d'erreur)
// =================================================================

/**
 * Calcule l'Impôt sur le Revenu (IR) Brut mensuel au Maroc.
 * 
 * @param float $sni Le Salaire Net Imposable mensuel.
 * @return array Contenant l'IR Brut, le Taux marginal, et la Somme à déduire.
 */
function calculer_ir_brut($sni) {
    // Barème mensuel de l'IR basé sur le document
    $bareme = [
        [2500.00, 0.00, 0.00],
        [4166.66, 0.10, 250.00],
        [5000.00, 0.20, 666.67],
        [6666.66, 0.30, 1166.67],
        [15000.00, 0.34, 1433.33],
    ];

    $taux = 0;
    $somme_a_deduire = 0;

    if ($sni <= 0) {
        return ['ir_brut' => 0, 'taux' => 0, 'somme_a_deduire' => 0];
    }

    foreach ($bareme as $tranche) {
        if ($sni <= $tranche[0]) {
            $taux = $tranche[1];
            $somme_a_deduire = $tranche[2];
            break;
        }
    }

    if ($sni > 15000.00) {
        $taux = 0.38;
        $somme_a_deduire = 2033.33;
    }

    $ir_brut = ($sni * $taux) - $somme_a_deduire;

    return ['ir_brut' => max(0, $ir_brut), 'taux' => $taux, 'somme_a_deduire' => $somme_a_deduire];
}

/**
 * Calcule l'IR Net.
 */
function calculer_ir_net($ir_brut, $nb_charges_famille) {
    // Déduction: 30 DH/personne, max 6 personnes
    $charges_plafonnees = min((int)$nb_charges_famille, 6);
    $deduction_famille = $charges_plafonnees * 30.00;
    
    $ir_net = $ir_brut - $deduction_famille;
    
    return max(0, $ir_net);
}


// =================================================================
// 2. LOGIQUE PRINCIPALE DU SCRIPT (Le calcul complet est conservé)
// =================================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Accès non autorisé.");
}

// Récupération des données du formulaire (Section Rémunération)
$salaire_base = (float)($_POST['salaire_base'] ?? 0);
$heures_supp = (float)($_POST['heures_supp'] ?? 0); 
$taux_anciennete = (float)($_POST['anciennete'] ?? 0);
$autres_primes = (float)($_POST['autres_primes'] ?? 0);

// Valeurs par défaut pour les champs retirés du formulaire (Charges Sociales et Familiales)
$avantages_nature = 0.00;
$taux_cimr = 0.00;
$nb_charges = 0;

// Calcul du Salaire Brut
$prime_anciennete = $salaire_base * $taux_anciennete;
$salaire_brut = $salaire_base + $heures_supp + $prime_anciennete + $autres_primes;
$sbg = $salaire_brut + $avantages_nature; 
$sbi = $sbg; 

// Calcul des Retenues Salariales (pour déterminer le Net à Payer)
$cnss_plafond = 6000.00; 
$base_cnss = min($sbi, $cnss_plafond);
$taux_cnss_sal = 0.0429; 
$deduction_cnss = $base_cnss * $taux_cnss_sal;

$taux_amo_sal = 0.0226; 
$deduction_amo = $sbi * $taux_amo_sal;

$taux_ipe_sal = 0.0019; 
$deduction_ipe = $sbi * $taux_ipe_sal;

$deduction_retraite = $sbi * $taux_cimr; 

// Calcul du SNI pour l'IR
$deduction_frais_pro = min(max(0, $sbi - $avantages_nature) * 0.20, 2500.00);

$cotisations_salariales_retenues = $deduction_cnss + $deduction_amo + $deduction_ipe + $deduction_retraite;

$sni = max(0, $sbi - $deduction_frais_pro - $cotisations_salariales_retenues);

// Calcul de l'IR Net
$ir_calcul = calculer_ir_brut($sni);
$ir_brut = $ir_calcul['ir_brut'];
$ir_net = calculer_ir_net($ir_brut, $nb_charges);

// SALAIRE NET À PAYER
$total_retenues = $cotisations_salariales_retenues + $ir_net; 
$salaire_net_a_payer = $sbg - $total_retenues;

// Calcul des Charges Patronales (pour information)
$cnss_plafond = 6000.00; 
$taux_cnss_pat = 0.0860; 
$taux_amo_pat = 0.0411; 
$taux_tfp_pat = 0.0160; 
$taux_ipe_pat = 0.0038; 
$cotisation_retraite_patronale = $sbi * $taux_cimr; 

$cotisation_cnss_patronale = min($sbi, $cnss_plafond) * $taux_cnss_pat;
$cotisation_amo_patronale = $sbi * $taux_amo_pat;
$cotisation_formation_patronale = $sbi * $taux_tfp_pat;
$cotisation_ipe_patronale = $sbi * $taux_ipe_pat;

$total_charges_patronales = (
    $cotisation_cnss_patronale + 
    $cotisation_amo_patronale + 
    $cotisation_formation_patronale +
    $cotisation_ipe_patronale +
    $cotisation_retraite_patronale
);


// =================================================================
// 3. AFFICHAGE DU BULLETIN (HTML - Affichage Minimaliste)
// =================================================================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat Bulletin de Paie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container result">
        <h2>FICHE DE PAIE </h2>
                
        <table class="minimal-result">
            <tr>
                <td colspan="2" class="header-result">Éléments de Rémunération</td>
            </tr>
            <tr>
                <td class="rubrique">Salaire de Base payé</td>
                <td class="montant"><?= number_format($salaire_base, 2, ',', ' ') ?> DH</td>
            </tr>
            <tr>
                <td class="rubrique">Prime Ancienneté (<?= $taux_anciennete * 100 ?>% du Salaire de Base)</td>
                <td class="montant"><?= number_format($prime_anciennete, 2, ',', ' ') ?> DH</td>
            </tr>
            <tr>
                <td class="rubrique">Autres Indemnités/Primes (HS, Jours Fériés, etc.)</td>
                <td class="montant"><?= number_format($heures_supp + $autres_primes, 2, ',', ' ') ?> DH</td>
            </tr>
            <tr>
                <td class="rubrique">Avantages Nature/Argent</td>
                <td class="montant"><?= number_format($avantages_nature, 2, ',', ' ') ?> DH</td>
            </tr>
             <tr>
                <td class="total">SALAIRE BRUT GLOBAL (SBG)</td>
                <td class="total montant"><?= number_format($sbg, 2, ',', ' ') ?> DH</td>
            </tr>
            
            <tr>
                <td colspan="2" class="header-final">NET À PAYER</td>
            </tr>
            <tr>
                <td class="final-total">NET À PAYER</td>
                <td class="final-total montant"><?= number_format($salaire_net_a_payer, 2, ',', ' ') ?> DH</td>
            </tr>
        </table>

        <div class="charges-patronales">
            <h4>Détails de Retenues (Pour information)</h4>
            <p>Total Retenues Salariales (Charges + IR): <?= number_format($total_retenues, 2, ',', ' ') ?> DH</p>
            <p>Total Charges Patronales: <?= number_format($total_charges_patronales, 2, ',', ' ') ?> DH</p>
        </div>

        <p><a href="index.html">Nouvelle Simulation</a></p>
        <p><A href="../dashboard/dashboard.php">Dashboard</a></p>
    </div>
</body>
</html>