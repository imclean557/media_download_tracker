<?php

namespace Drupal\media_download_tracker\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Track files downloaded via the Media Entity Download.
 */
class MediaDownloadTrackerSubscriber implements EventSubscriberInterface {

  /**
   * The database connection.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The time service.
   *
   * @var Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new MediaDownloadTrackerSubscriber.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user service.
   */
  public function __construct(Connection $database, TimeInterface $time, AccountProxyInterface $current_user) {
    $this->database = $database;
    $this->time = $time;
    $this->currentUser = $current_user;
  }

  /**
   * Subscribe to Request event.
   */
  public static function getSubscribedEvents(): array {
    $events[KernelEvents::REQUEST][] = ['onRequest', 0];
    return $events;
  }

  /**
   * Function to run on the request event.
   */
  public function onRequest(RequestEvent $event) {
    $request = $event->getRequest();
    // Check for Media Entity Download route.
    if ($request->attributes->get('_route') === 'media_entity_download.download') {
      $media_id = $request->attributes->get('media')->id();
      $requested_url = $request->getUri();
      $referrer = $request->headers->get('referer');
      $ip_address = $request->getClientIp();

      // Log download data in the database.
      $this->database->insert('media_download_tracker')
        ->fields([
          'media_id' => $media_id,
          'timestamp' => $this->time->getRequestTime(),
          'uid' => $this->currentUser->id(),
          'requested_url' => $requested_url,
          'referrer' => $referrer,
          'ip_address' => $ip_address,
        ])
        ->execute();
    }
  }

}
