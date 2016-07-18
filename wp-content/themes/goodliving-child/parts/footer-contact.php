<?php $phone = false; ?>
<?php $add1 = false; ?>
<?php $add2 = false; ?>
<?php $locality = false; ?>
<?php $region = false; ?>
<?php $postcode = false; ?>
<?php $email = false; ?>

<?php $phone = of_get_option('contact_telephone'); ?>
<?php $add1 = of_get_option('contact_address_street_address_1'); ?>
<?php $add2 = of_get_option('contact_address_street_address_2'); ?>
<?php $locality = of_get_option('contact_address_locality'); ?>
<?php $region = of_get_option('contact_address_region'); ?>
<?php $postcode = of_get_option('contact_address_post_code'); ?>
<?php $email = of_get_option('contact_email'); ?>

<div class="footer__contact">
    <span class="footer__address">
        Easy Sitges
        <?php if($add1 && "" != $add1): ?>
            <?php echo $add1; ?>
        <?php endif; ?>

        <?php if($add2 && "" != $add2): ?>
            <?php echo $add2; ?>
        <?php endif; ?>

        <?php if($locality && "" != $locality): ?>
            <?php echo $locality; ?>
        <?php endif; ?>

        <?php if($region && "" != $region): ?>
            <?php echo $region; ?>
        <?php endif; ?>

        <?php if($postcode && "" != $postcode): ?>
            <?php echo $postcode; ?>
        <?php endif; ?>
    </span>

    <span class="footer__phone">
        <?php if($phone && "" != $phone): ?>
            Tel: <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
        <?php endif; ?>
    </span>

    <span class="footer_email">
        <?php if($email && "" != $email): ?>
            Email: <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
        <?php endif; ?>
    </span>
</div>
