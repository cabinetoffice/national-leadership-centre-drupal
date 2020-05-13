<?php

namespace Drupal\nlc_library\Model\Trello;

class CardAttachmentModel extends AbstractTrelloModel {

  /**
   * @var string
   */
  protected $parentIdParam = 'idMember';

  /**
   * @var bool
   */
  protected $isUpload;

  /**
   * The Trello URL.
   *
   * @var string
   */
  protected $url;

  public function __construct($object) {
    parent::__construct($object);

    $this->url = $object->url;
    $this->isUpload = $object->isUpload;
  }

  public function modelType(): string {
    return 'attachment';
  }

  /**
   * @return string
   */
  public function getUrl(): string {
    return $this->url;
  }

}
