-- ---------------------------------------------------------------------
-- Mail templates for flatfeedelivery
-- ---------------------------------------------------------------------
-- First, delete existing entries
SET @var := 0;
SELECT @var := `id` FROM `message` WHERE name="order_confirmation_flatfeedelivery";
DELETE FROM `message` WHERE `id`=@var;
-- Try if ON DELETE constraint isn't set
DELETE FROM `message_i18n` WHERE `id`=@var;

-- Then add new entries
SELECT @max := MAX(`id`) FROM `message`;
SET @max := @max+1;
-- insert message
INSERT INTO `message` (`id`, `name`, `secured`) VALUES
  (@max,
   'order_confirmation_flatfeedelivery',
   '0'
  );
-- and template fr_FR
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max,
   'fr_FR',
   'order confirmation_flatfeedelivery',
   'Envoi de la commande : {$order_ref}',
   'La  commande : {$order_ref} a été expédiée.',
   'La  commande :{$order_ref} a été expédiée.'
  );
-- and en_US
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max,
   'en_US',
   'order confirmation_flatfeedelivery',
   'Envoi de la commande : {$order_ref}',
   'La  commande : {$order_ref} a été expédiée.',
   'La  commande :{$order_ref} a été expédiée.'
  );

