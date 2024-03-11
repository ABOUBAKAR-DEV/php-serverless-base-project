namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;


class ExceptionLoggerListener
{
    private $logger;
    private $appLocal;

    public function __construct(LoggerInterface $logger, string $appLocal)
    {
        $this->logger = $logger;
        $this->appLocal = $appLocal;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->appLocal === 'true') {
            $exception = $event->getThrowable();
            $message = sprintf(
                "[%s] %s: %s (uncaught exception) at %s line %s",
                date('Y-m-d H:i:s'),
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );

            $logFilename = 'log-' . date('Y-m-d_H-i-s') . '.log';
            $this->logger->error($message, ['filename' => $logFilename]);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}