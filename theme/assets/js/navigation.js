(function(){
  'use strict';

  document.documentElement.classList.add('js');
  var body = document.body;
  var globalButton = document.querySelector('[data-global-menu]');
  var globalPanel = document.querySelector('[data-global-menu-panel]');
  var leftPanel = document.querySelector('[data-leftbar-open]') ? document.getElementById('docszine-leftbar') : null;
  var rightPanel = document.querySelector('[data-rightbar-open]') ? document.getElementById('docszine-rightbar') : null;
  var leftOpen = document.querySelector('[data-leftbar-open]');
  var rightOpen = document.querySelector('[data-rightbar-open]');
  var leftClose = document.querySelector('[data-leftbar-close]');
  var rightClose = document.querySelector('[data-rightbar-close]');
  var backdrop = document.querySelector('[data-drawer-backdrop]');
  var lastTrigger = null;

  function compact(){
    return window.matchMedia('(max-width:1179px)').matches;
  }

  function releasePanel(panel){
    if(!panel) return;
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden','false');
    panel.removeAttribute('inert');
  }

  function hidePanel(panel, trigger){
    if(!panel) return;
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden','true');
    panel.setAttribute('inert','');
    if(trigger) trigger.setAttribute('aria-expanded','false');
  }

  function closeAll(restoreFocus){
    if(globalButton) globalButton.setAttribute('aria-expanded','false');
    if(globalPanel){
      globalPanel.hidden = true;
      globalPanel.setAttribute('aria-hidden','true');
      globalPanel.classList.remove('open');
    }
    if(compact()){
      hidePanel(leftPanel,leftOpen);
      hidePanel(rightPanel,rightOpen);
    } else {
      releasePanel(leftPanel);
      releasePanel(rightPanel);
    }
    if(backdrop){ backdrop.hidden = true; }
    body.classList.remove('no-scroll');
    if(restoreFocus && lastTrigger && document.contains(lastTrigger)) lastTrigger.focus();
    lastTrigger = null;
  }

  function openPanel(panel,trigger,closeButton){
    if(!panel || !compact()) return;
    closeAll(false);
    lastTrigger = trigger;
    trigger.setAttribute('aria-expanded','true');
    panel.setAttribute('aria-hidden','false');
    panel.removeAttribute('inert');
    panel.classList.add('is-open');
    if(backdrop) backdrop.hidden = false;
    body.classList.add('no-scroll');
    if(closeButton) closeButton.focus();
  }

  function syncPanels(){
    if(compact()){
      hidePanel(leftPanel,leftOpen);
      hidePanel(rightPanel,rightOpen);
    } else {
      releasePanel(leftPanel);
      releasePanel(rightPanel);
      if(backdrop) backdrop.hidden = true;
      body.classList.remove('no-scroll');
    }
  }

  syncPanels();

  if(globalButton && globalPanel){
    globalButton.addEventListener('click', function(){
      var open = globalButton.getAttribute('aria-expanded') !== 'true';
      closeAll(false);
      if(open){
        lastTrigger = globalButton;
        globalButton.setAttribute('aria-expanded','true');
        globalPanel.hidden = false;
        globalPanel.setAttribute('aria-hidden','false');
        globalPanel.classList.add('open');
        body.classList.add('no-scroll');
        var firstControl = globalPanel.querySelector('a[href], input:not([disabled]), button:not([disabled])');
        if(firstControl) firstControl.focus();
      }
    });
    globalPanel.addEventListener('click', function(event){ if(event.target.closest('a')) closeAll(false); });
  }

  if(leftOpen) leftOpen.addEventListener('click', function(){ openPanel(leftPanel,leftOpen,leftClose); });
  if(rightOpen) rightOpen.addEventListener('click', function(){ openPanel(rightPanel,rightOpen,rightClose); });
  if(leftClose) leftClose.addEventListener('click', function(){ closeAll(true); });
  if(rightClose) rightClose.addEventListener('click', function(){ closeAll(true); });
  if(backdrop) backdrop.addEventListener('click', function(){ closeAll(true); });
  if(leftPanel) leftPanel.addEventListener('click', function(event){ if(event.target.closest('a')) closeAll(false); });
  if(rightPanel) rightPanel.addEventListener('click', function(event){ if(event.target.closest('a')) closeAll(false); });

  document.addEventListener('click', function(event){
    var toggle = event.target.closest('[data-docszine-tree-toggle]');
    if(!toggle) return;
    var node = toggle.closest('.docszine-tree-node');
    if(!node) return;
    var open = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
    node.classList.toggle('is-open', !open);
  });

  document.addEventListener('keydown', function(event){
    if(event.key === 'Escape'){
      closeAll(true);
      return;
    }
    if(event.key !== 'Tab') return;
    var panel = leftPanel && leftPanel.classList.contains('is-open') ? leftPanel : (rightPanel && rightPanel.classList.contains('is-open') ? rightPanel : (globalPanel && globalPanel.classList.contains('open') ? globalPanel : null));
    if(!panel) return;
    var focusable = Array.prototype.slice.call(panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'));
    if(!focusable.length) return;
    var first = focusable[0];
    var last = focusable[focusable.length - 1];
    if(event.shiftKey && document.activeElement === first){ event.preventDefault(); last.focus(); }
    else if(!event.shiftKey && document.activeElement === last){ event.preventDefault(); first.focus(); }
  });

  var mediaQuery = window.matchMedia('(max-width:1179px)');
  var mediaHandler = function(){ closeAll(false); syncPanels(); };
  if(mediaQuery.addEventListener) mediaQuery.addEventListener('change',mediaHandler);
  else if(mediaQuery.addListener) mediaQuery.addListener(mediaHandler);
})();
