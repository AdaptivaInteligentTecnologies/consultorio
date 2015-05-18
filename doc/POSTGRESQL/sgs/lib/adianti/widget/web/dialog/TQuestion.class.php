<?php
/**
 * Question Dialog
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuestion
{
    private $id;
    
    /**
     * Class Constructor
     * @param  $message    A string containint the question
     * @param  $action_yes Action taken for YES response
     * @param  $action_no  Action taken for NO  response
     * @param  $title_msg  Dialog Title
     */
    public function __construct($message, TAction $action_yes = NULL, TAction $action_no = NULL, $title_msg = '')
    {
        $this->id = uniqid();
        
        $modal_wrapper = new TElement('div');
        $modal_wrapper->{'class'} = 'modal fade';
        $modal_wrapper->{'id'}    = $this->id;
        $modal_wrapper->{'style'} = 'margin-top: 10%; z-index:4000';
        $modal_wrapper->{'tabindex'} = '-1';
        
        $modal_dialog = new TElement('div');
        $modal_dialog->{'class'} = 'modal-dialog';
        
        $modal_content = new TElement('div');
        $modal_content->{'class'} = 'modal-content';
        
        $modal_header = new TElement('div');
        $modal_header->{'class'} = 'modal-header';
        
        $image = new TImage("lib/adianti/images/question.png");
        $image->{'style'} = 'float:left';
        
        $close = new TElement('button');
        $close->{'type'} = 'button';
        $close->{'class'} = 'close';
        $close->{'data-dismiss'} = 'modal';
        $close->{'aria-hidden'} = 'true';
        $close->add('×');
        
        $title = new TElement('h4');
        $title->{'class'} = 'modal-title';
        $title->{'style'} = 'display:inline';
        $title->add( $title_msg ? $title_msg : TAdiantiCoreTranslator::translate('Question') );
        
        $body = new TElement('div');
        $body->{'class'} = 'modal-body';
        $body->add($image);
        
        $span = new TElement('span');
        $span->{'display'} = 'block';
        $span->{'style'} = 'margin-left:20px;float:left';
        $span->add($message);
        $body->add($span);
        
        $footer = new TElement('div');
        $footer->{'class'} = 'modal-footer';
        
        if ($action_yes)
        {
            $button = new TElement('button');
            $button->{'class'} = 'btn btn-default';
            $button->{'data-toggle'}="modal";
            $button->add(TAdiantiCoreTranslator::translate('Yes'));
            $button->{'onclick'} = '__adianti_load_page(\''.$action_yes->serialize() . '\')';
            $footer->add($button);
        }
        
        if ($action_no)
        {
            $button = new TElement('button');
            $button->{'class'} = 'btn btn-default';
            $button->{'data-toggle'}="modal";
            $button->add(TAdiantiCoreTranslator::translate('No'));
            $button->{'onclick'} = '__adianti_load_page(\''.$action_no->serialize() . '\')';
            $footer->add($button);
        }
        else
        {
            $button = new TElement('button');
            $button->{'class'} = 'btn btn-default';
            $button->{'data-dismiss'} = 'modal';
            $button->add(TAdiantiCoreTranslator::translate('No'));
            $footer->add($button);
        }
        
        $button = new TElement('button');
        $button->{'class'} = 'btn btn-default';
        $button->{'data-dismiss'} = 'modal';
        $button->add(TAdiantiCoreTranslator::translate('Cancel'));
        $footer->add($button);
        
        $modal_wrapper->add($modal_dialog);
        $modal_dialog->add($modal_content);
        $modal_content->add($modal_header);
        $modal_header->add($close);
        $modal_header->add($title);
        
        $modal_content->add($body);
        $modal_content->add($footer);
        
        $modal_wrapper->show();
        
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add(' $(document).ready(function() {
            $("#'.$this->id.'").modal({backdrop:true, keyboard:true});
            });');
        $script->show();
    }
}
?>