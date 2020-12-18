<?php

namespace Drupal\ga_core\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\telephone\Plugin\Field\FieldWidget\TelephoneDefaultWidget;

/**
 * Plugin implementation of the 'ga_telephone_number_default' widget.
 *
 * @FieldWidget(
 *   id = "ga_telephone_number_default",
 *   module = "ga_core",
 *   label = @Translation("GA - Telephone Number"),
 *   field_types = {
 *     "ga_telephone_number"
 *   }
 * )
 */
class GaTelephoneNumberWidget extends TelephoneDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // We want the main value element to have a visible title.
    $element['value']['#title'] = $this->t('Phone number');
    $element['value']['#title_display'] = 'before';
    $element['value']['#weight'] = -98;

    // We also want to add elements for our other 4 custom columns.
    $element['type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number Type'),
      '#description' => $this->t('Primary, Phone, Mobile, Fax, etc.'),
      '#default_value' => $items[$delta]->type ?? $this->t('Primary'),
      '#weight' => -99,
      '#size' => 22,
    ];
    $element['extension'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Extension'),
      '#default_value' => $items[$delta]->extension ?? NULL,
      '#size' => 8,
    ];
    $element['instructions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone tree instructions'),
      '#default_value' => $items[$delta]->instructions ?? NULL,
      '#size' => 40,
      '#maxlength_js' => TRUE,
      '#maxlength' => 25,
    ];

    return $element;
  }

}
