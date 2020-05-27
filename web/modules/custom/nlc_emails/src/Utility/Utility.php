<?php


namespace Drupal\nlc_emails\Utility;

use Drupal\nlc_emails\Emails\NlcEmailHandlerInterface;

class Utility {

  /**
   * Splits an internal ID into its two parts.
   *
   * Both internal item IDs and combined property paths are prefixed with the
   * corresponding datasource ID. This method will split these IDs up again into
   * their two parts.
   *
   * @param string $combined_id
   *   The internal ID, with an optional datasource prefix separated with
   *   \Drupal\search_api\IndexInterface::DATASOURCE_ID_SEPARATOR from the
   *   raw item ID or property path.
   *
   * @return array
   *   A numeric array, containing the datasource ID in element 0 and the raw
   *   item ID or property path in element 1. In the case of
   *   datasource-independent properties (that is, when there is no prefix),
   *   element 0 will be NULL.
   */
  public static function splitCombinedId($combined_id) {
    if (strpos($combined_id, NlcEmailHandlerInterface::DATASOURCE_ID_SEPARATOR) !== FALSE) {
      return explode(NlcEmailHandlerInterface::DATASOURCE_ID_SEPARATOR, $combined_id, 2);
    }
    return [NULL, $combined_id];
  }
}
