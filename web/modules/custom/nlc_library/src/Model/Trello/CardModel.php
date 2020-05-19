<?php

namespace Drupal\nlc_library\Model\Trello;

class CardModel extends AbstractTrelloModel {

  const DATE_FORMAT = 'Y-m-d\TH:i:s.v\z';

  /**
   * @var string
   */
  protected $description;

  /**
   * The Trello URL.
   *
   * @var string
   */
  protected $url;

  /**
   * The Trello short URL.
   *
   * @var string
   */
  protected $shortUrl;

  /**
   * @var string
   */
  protected $dateLastActivity;

  /**
   * @var string
   */
  protected $listId;

  /**
   * @var string[]
   */
  protected $labelIds = [];

  /**
   * @var \Drupal\nlc_library\Model\Trello\CardAttachmentModel[]
   */
  protected $attachments;

  /**
   * @var \Drupal\taxonomy\TermInterface
   */
  protected $topicTerm;

  /**
   * @var \Drupal\taxonomy\TermInterface
   */
  protected $labelTerm;

  /**
   * @var \DateTimeZone;
   */
  protected $tz;

  /**
   * @var \Drupal\nlc_library\Model\Trello\CustomFieldModel[]
   */
  protected $customFieldModels;

  /**
   * @var \Drupal\nlc_library\Model\Trello\CustomFieldItemModel[]
   */
  protected $customFieldItems;

  /**
   * CardModel constructor.
   *
   * @param $object
   * @param \Drupal\nlc_library\Model\Trello\CustomFieldModel[] $customFields
   */
  public function __construct($object, array $customFields) {
    parent::__construct($object);

    $this->description = $object->desc;
    $this->url = $object->url;
    $this->shortUrl = $object->shortUrl;
    $this->dateLastActivity = $object->dateLastActivity;
    $this->listId = $object->idList;
    $this->labelIds = $object->idLabels;
    $this->tz = new \DateTimeZone('Europe/London');
    $this->setAttachments($object->attachments ? $object->attachments : []);
    foreach ($customFields as $customField) {
      $this->setCustomField($customField);
    }
    $this->setCustomFieldItems($object->customFieldItems ? $object->customFieldItems : []);
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'card';
  }

  /**
   * @return string
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * Get the Trello URL for this item.
   *
   * @return string
   */
  public function getUrl(): string {
    return $this->url;
  }

  /**
   * Get the Trello short URL for this item.
   *
   * @return string
   */
  public function getShortUrl(): string {
    return $this->shortUrl;
  }

  /**
   * @return string
   */
  public function getDateLastActivity(): string {
    return $this->dateLastActivity;
  }

  /**
   * @return \DateTime|false
   */
  public function getLastActivityDateTime(): ?\DateTime {
    $time = strtotime($this->getDateLastActivity());
    $dateTime =  new \DateTime();
    $dateTime->setTimezone($this->tz);
    $dateTime->setTimestamp($time);
    return $dateTime ? $dateTime : null;
  }

  /**
   * @return string
   */
  public function getListId(): string {
    return $this->listId;
  }

  /**
   * @return string[]
   */
  public function getLabelIds(): array {
    return $this->labelIds;
  }

  /**
   * @param \Drupal\taxonomy\TermInterface $topicTerm
   */
  public function setTopicTerm(\Drupal\taxonomy\TermInterface $topicTerm): void {
    $this->topicTerm = $topicTerm;
  }

  /**
   * @return \Drupal\taxonomy\TermInterface
   */
  public function getTopicTerm(): \Drupal\taxonomy\TermInterface {
    return $this->topicTerm;
  }

  /**
   * @param \Drupal\taxonomy\TermInterface $labelTerm
   */
  public function setLabelTerm(\Drupal\taxonomy\TermInterface $labelTerm): void {
    $this->labelTerm = $labelTerm;
  }

  /**
   * @return \Drupal\taxonomy\TermInterface
   */
  public function getLabelTerm(): \Drupal\taxonomy\TermInterface {
    return $this->labelTerm;
  }

  /**
   * @param object[] $attachments
   */
  protected function setAttachments(array $attachments): void {
    foreach ($attachments as $attachment) {
      $this->attachments[] = new CardAttachmentModel($attachment);
    }
  }

  /**
   * @param \Drupal\nlc_library\Model\Trello\CustomFieldModel $model
   */
  protected function setCustomField(CustomFieldModel $model) {
    $this->customFieldModels[$model->getId()] = $model;
  }

  /**
   * @param array $customFieldItems
   */
  protected function setCustomFieldItems(array $customFieldItems): void {
    foreach ($customFieldItems as $customFieldItem) {
      $customFieldItem = new CustomFieldItemModel($customFieldItem);
      $customFieldModel = $this->customFieldModels[$customFieldItem->getIdCustomField()];
      $customFieldItem->setCustomFieldModel($customFieldModel);
      $this->customFieldItems[$customFieldItem->getCustomFieldModel()->getConnectFieldName()] = $customFieldItem;
    }
  }

  /**
   * @param $fieldName
   *
   * @return \Drupal\nlc_library\Model\Trello\CustomFieldItemModel|null
   */
  public function getCustomFieldItem($fieldName) {
    return $this->customFieldItems[$fieldName] ? $this->customFieldItems[$fieldName] : null;
  }

  /**
   * @return \Drupal\nlc_library\Model\Trello\CustomFieldItemModel[]
   */
  public function getCustomFieldItems() {
    return $this->customFieldItems;
  }

  /**
   * @return \Drupal\nlc_library\Model\Trello\CardAttachmentModel[]|null
   */
  public function getAttachments(): ?array {
    return $this->attachments;
  }

  /**
   * @return \Drupal\nlc_library\Model\Trello\CardAttachmentModel|null
   */
  public function getFirstAttachment(): ?CardAttachmentModel {
    return current ($this->getAttachments());
  }

}
