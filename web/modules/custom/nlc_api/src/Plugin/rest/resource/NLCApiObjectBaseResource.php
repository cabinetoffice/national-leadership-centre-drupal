<?php


namespace Drupal\nlc_api\Plugin\rest\resource;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class NLCApiObjectBaseResource extends NLCApiBaseResource implements NLCApiObjectBaseInterface {

}
