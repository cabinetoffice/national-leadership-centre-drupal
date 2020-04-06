<?php


namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a link to the Esurv COVID-19 response wellness survey.
 *
 * @Block (
 *   id = "covid_19_esurv_survey_block",
 *   admin_label = @Translation("Esurv COVID-19 wellness survey block"),
 * )
 * @package Drupal\nlc_prototype\Plugin\Block
 */
class Covid19EsurvSurveyBlock extends BlockBase {

  /**
   * The block body, that allows a Connect user to click on the link to the Esurv survey.
   *
   * @return array
   */
  public function build() {
    $build = [];
    $build['#attributes'] = [
      'class' => [
        'container--negative',
        'nlc-banner-block',
      ],
    ];
    $build['#prefix'] = '<div class="govuk-width-container">';
    $build['#suffix'] = '</div>';
    $surveyUrl = Url::fromUri('https://www.smartsurvey.co.uk/s/CV19Response_060420/');
    $surveyLink = Link::fromTextAndUrl($this->t('Complete survey'), $surveyUrl)->toRenderable();
    $surveyLink['#attributes'] = [
      'target' => '_blank',
      'class' => [
        'button'
      ],
    ];
    $email = 'NLC@CabinetOffice.gov.uk';
    $mailUrl = Url::fromUri('mailto:' . $email);
    $mailLink = Link::fromTextAndUrl($email, $mailUrl);
    $build['body'] = [
      '#type' => 'inline_template',
      '#template' => '
      <section aria-label="COVID-19 action required" id="nlc-covid-19-action-required">
        {{ heading }}
        <p>{{ body_1 }}</p>
        <p>{{ body_2 }}</p>
        <p>{{ body_3 }}</p>
        {{ link }}
      </section>',
      '#context' => [
        'heading' => [
          '#theme' => 'nlc_alert_title',
          '#type' => 'h2',
          '#title' => $this->t('COVID-19: Action Required'),
        ],
        'body_1' => $this->t("If we are to tackle this crisis effectively, it is essential that you provide us with an accurate and up-to-date picture of the situation on the ground in your area. Please take 5 minutes to answer some key questions that will help with the Government's planning process."),
        'body_2' => $this->t('If you cannot access the survey, please contact the NLC at @email.', ['@email' => $mailLink->toString()]),
        'body_3' => $this->t('Responses are anonymous and will be treated in confidence.'),
        'link' => $surveyLink,
      ],
    ];

    return $build;
  }

}
