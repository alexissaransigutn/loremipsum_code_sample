<?php

namespace Drupal\Tests\ga_core;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\Tests\RandomGeneratorTrait;

/**
 * Trait with helpers to handle media items in tests.
 *
 * @group ga_dtt
 */
trait GaMediaHelpersTestTrait {

  use RandomGeneratorTrait;

  /**
   * Helper to generate a document media item.
   *
   * @param string $filename
   *   (optional) The name to use as the source file. A fake file will be
   *   created with this name. Defaults to a random machine name + .pdf.
   * @param string $name
   *   (optional) The document media name. If empty, the filename will be used.
   *   Defaults to an empty string.
   * @param bool $status
   *   (optional) The status to create the media in. Defaults to TRUE.
   *
   * @return \Drupal\media\MediaInterface
   *   An unsaved media item.
   */
  protected function generateDocument($filename = '', $name = '', $status = TRUE) {
    if (empty($filename)) {
      $filename = $this->randomMachineName() . '.pdf';
    }
    file_put_contents('public://' . $filename, $this->randomMachineName(64));

    $file = File::create([
      'uri' => 'public://' . $filename,
      'uid' => 1,
    ]);
    $file->setPermanent();
    $file->save();

    return Media::create([
      'bundle' => 'document',
      'name' => empty($name) ? $filename : $name,
      'field_media_file' => [
        'target_id' => $file->id(),
      ],
      'status' => $status,
      'uid' => 1,
    ]);
  }

  /**
   * Helper to generate an image media item.
   *
   * @param string $filename
   *   (optional) The name to use as the source file. A fake file will be
   *   created with this name. Defaults to a random machine name + .jpg.
   * @param string $name
   *   (optional) The image media name. If empty, the filename will be used.
   *   Defaults to an empty string.
   * @param bool $status
   *   (optional) The status to create the media in. Defaults to TRUE.
   *
   * @return \Drupal\media\MediaInterface
   *   An unsaved media item.
   */
  protected function generateImage($filename = '', $name = '', $status = TRUE) {
    if (empty($filename)) {
      $filename = $this->randomMachineName() . '.jpg';
    }
    file_put_contents('public://' . $filename, $this->randomMachineName(64));

    $file = File::create([
      'uri' => 'public://' . $filename,
      'uid' => 1,
    ]);
    $file->setPermanent();
    $file->save();

    return Media::create([
      'bundle' => 'image',
      'name' => empty($name) ? $filename : $name,
      'field_media_image' => [
        'target_id' => $file->id(),
      ],
      'status' => $status,
      'uid' => 1,
    ]);
  }

}
