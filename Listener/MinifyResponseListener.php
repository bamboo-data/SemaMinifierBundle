<?php

namespace Sema\Bundle\MinifierBundle\Listener;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * PageService
 */
class MinifyResponseListener
{
    /**
     * @var bool
     */
    protected $enableListener = false;

    /**
     * Constructor Listener
     * @param enableListener $enableListener enable minify
     */
    public function __construct($enableListener = null)
    {
        $this->enableListener = $enableListener;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if($this->enableListener) {
            $response = $event->getResponse();

            if (
                $response instanceof BinaryFileResponse
                || $response instanceof StreamedResponse
                || (class_exists('\eZ\Bundle\EzPublishIOBundle\BinaryStreamResponse') && $response instanceof \eZ\Bundle\EzPublishIOBundle\BinaryStreamResponse)
            ) {
                return;
            }

            $content = \Minify_HTML::minify($response->getContent(), array('cssMinifier' => array('Minify_CSS', 'minify'),
                                                                           'jsMinifier' => array('JSMinPlus', 'minify'),
                                                                           'xhtml' => false)
             );
            $response->setContent($content);
        }

        return;
    }
}
