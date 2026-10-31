(function(){
  'use strict';
  var root=document.documentElement, body=document.body;
  root.classList.add('js');
  var header=document.getElementById('masthead'), localBar=document.querySelector('.docszine-local-bar'), toolbar=document.getElementById('wpadminbar');
  var globalButton=document.querySelector('[data-global-menu]'), globalPanel=document.querySelector('[data-global-menu-panel]');
  var backdrop=document.querySelector('[data-drawer-backdrop]');
  var panels=[document.getElementById('docszine-leftbar'),document.getElementById('docszine-rightbar')];
  var triggers=[document.querySelector('[data-leftbar-open]'),document.querySelector('[data-rightbar-open]')];
  var active=null, returnFocus=null, inertNodes=[], frame=0, previousWidth=window.innerWidth;
  var compactQuery=window.matchMedia('(max-width:1179px)');
  function compact(){return compactQuery.matches;}
  function visible(element){return element && element.getClientRects().length && getComputedStyle(element).visibility!=='hidden';}
  function controls(panel){
    return Array.prototype.slice.call(panel.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')).filter(function(element){return visible(element) && !element.closest('[inert]');});
  }
  function restoreBackground(){
    inertNodes.forEach(function(item){if(!item.hadInert)item.element.removeAttribute('inert');});
    inertNodes=[];
  }
  function isolate(panel){
    var current=panel;
    while(current && current!==body){
      var parent=current.parentElement;
      if(!parent)break;
      Array.prototype.forEach.call(parent.children,function(sibling){
        if(sibling===current || sibling===backdrop || /^(SCRIPT|STYLE|LINK)$/.test(sibling.tagName))return;
        inertNodes.push({element:sibling,hadInert:sibling.hasAttribute('inert')});
        sibling.setAttribute('inert','');
      });
      current=parent;
    }
  }
  function geometry(){
    frame=0;
    var bar=toolbar && visible(toolbar)?toolbar.getBoundingClientRect():null;
    var barHeight=bar?Math.max(0,Math.min(window.innerHeight,bar.bottom)-Math.max(0,bar.top)):0;
    root.style.setProperty('--dz-bar-visible',barHeight+'px');
    if(header)root.style.setProperty('--header-h',header.getBoundingClientRect().height+'px');
    var localHeight=localBar && visible(localBar)?localBar.getBoundingClientRect().height:0;
    root.style.setProperty('--dz-local-active',localHeight+'px');
  }
  function scheduleGeometry(){if(!frame)frame=window.requestAnimationFrame(geometry);}
  function syncPanels(){
    panels.forEach(function(panel,index){
      if(!panel)return;
      var open=panel===active;
      panel.classList.toggle('is-open',open);
      panel.setAttribute('aria-hidden',compact() && !open?'true':'false');
      if(compact() && !open)panel.setAttribute('inert',''); else panel.removeAttribute('inert');
      if(open){panel.setAttribute('role','dialog');panel.setAttribute('aria-modal','true');}
      else{panel.removeAttribute('role');panel.removeAttribute('aria-modal');}
      if(triggers[index])triggers[index].setAttribute('aria-expanded',open?'true':'false');
    });
  }
  function closeAll(restoreFocus){
    var previous=returnFocus;
    active=null;returnFocus=null;restoreBackground();
    if(globalButton)globalButton.setAttribute('aria-expanded','false');
    if(globalPanel){globalPanel.hidden=true;globalPanel.setAttribute('aria-hidden','true');globalPanel.classList.remove('open');}
    if(backdrop)backdrop.hidden=true;
    body.classList.remove('no-scroll');
    syncPanels();
    if(restoreFocus && previous){
      var target=visible(previous)?previous:document.getElementById('primary');
      if(target)target.focus({preventScroll:true});
    }
  }
  function openPanel(panel,trigger){
    if(!panel || !compact())return;
    closeAll(false);active=panel;returnFocus=trigger;syncPanels();isolate(panel);
    if(backdrop)backdrop.hidden=false;
    body.classList.add('no-scroll');
    var first=controls(panel)[0];if(first)first.focus({preventScroll:true});
  }
  panels.forEach(function(panel,index){
    if(!panel)return;
    if(triggers[index])triggers[index].addEventListener('click',function(){openPanel(panel,triggers[index]);});
    var close=panel.querySelector('.close-drawer');
    if(close)close.addEventListener('click',function(){closeAll(true);});
    panel.addEventListener('click',function(event){if(event.target.closest('a'))closeAll(false);});
  });
  if(backdrop)backdrop.addEventListener('click',function(){closeAll(true);});
  if(globalButton && globalPanel){
    globalButton.addEventListener('click',function(){
      var open=globalButton.getAttribute('aria-expanded')!=='true';closeAll(false);
      if(open){returnFocus=globalButton;globalButton.setAttribute('aria-expanded','true');globalPanel.hidden=false;globalPanel.setAttribute('aria-hidden','false');globalPanel.classList.add('open');}
    });
    globalPanel.addEventListener('click',function(event){if(event.target.closest('a'))closeAll(false);});
  }
  document.addEventListener('click',function(event){
    var toggle=event.target.closest('[data-docszine-tree-toggle]');if(!toggle)return;
    var node=toggle.closest('.docszine-tree-node');if(!node)return;
    var open=toggle.getAttribute('aria-expanded')!=='true';toggle.setAttribute('aria-expanded',open?'true':'false');node.classList.toggle('is-open',open);
  });
  document.addEventListener('keydown',function(event){
    if(event.key==='Escape'){closeAll(true);return;}
    if(event.key!=='Tab' || !active)return;
    var list=controls(active);if(!list.length){event.preventDefault();return;}
    var first=list[0],last=list[list.length-1];
    if(event.shiftKey && (document.activeElement===first || !active.contains(document.activeElement))){event.preventDefault();last.focus();}
    else if(!event.shiftKey && (document.activeElement===last || !active.contains(document.activeElement))){event.preventDefault();first.focus();}
  });
  document.addEventListener('focusin',function(event){
    if(active && !active.contains(event.target)){var first=controls(active)[0];if(first)first.focus();}
  });
  window.addEventListener('scroll',scheduleGeometry,{passive:true});
  window.addEventListener('resize',function(){
    if(window.innerWidth!==previousWidth){previousWidth=window.innerWidth;closeAll(true);}
    scheduleGeometry();
  });
  window.addEventListener('pageshow',function(){closeAll(false);scheduleGeometry();});
  if(window.visualViewport)window.visualViewport.addEventListener('resize',scheduleGeometry);
  if('ResizeObserver' in window){var observer=new ResizeObserver(scheduleGeometry);[header,localBar,toolbar].forEach(function(element){if(element)observer.observe(element);});}
  var scrollLabel=(window.docszineNavigation || {}).scrollRegion || 'Scrollable content. Use the arrow keys to read more.';
  var scrollRegions=Array.prototype.map.call(document.querySelectorAll('.docszine-prose pre,.docszine-prose .wp-block-table,.docszine-prose>table'),function(element){
    return {element:element,tab:false,role:false,label:false};
  });
  function enhanceScrollRegions(){
    scrollRegions.forEach(function(item){
      var element=item.element, overflowing=element.scrollWidth>element.clientWidth+1;
      if(overflowing){
        if(!element.hasAttribute('tabindex')){element.setAttribute('tabindex','0');item.tab=true;}
        if(element.tagName!=='TABLE' && !element.hasAttribute('role')){element.setAttribute('role','region');item.role=true;}
        if(!element.hasAttribute('aria-label') && !element.hasAttribute('aria-labelledby')){element.setAttribute('aria-label',scrollLabel);item.label=true;}
      } else {
        if(item.tab && element.getAttribute('tabindex')==='0')element.removeAttribute('tabindex');
        if(item.role && element.getAttribute('role')==='region')element.removeAttribute('role');
        if(item.label && element.getAttribute('aria-label')===scrollLabel)element.removeAttribute('aria-label');
        item.tab=false;item.role=false;item.label=false;
      }
    });
  }
  window.addEventListener('resize',enhanceScrollRegions);
  if(document.fonts)document.fonts.ready.then(enhanceScrollRegions);
  syncPanels();geometry();enhanceScrollRegions();
})();
