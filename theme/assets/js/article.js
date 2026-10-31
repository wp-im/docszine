(function(){
  'use strict';
  var links = Array.prototype.slice.call(document.querySelectorAll('.docszine-toc a[href^="#"]'));
  if(links.length && 'IntersectionObserver' in window){
    function targetId(link){
      try { return decodeURIComponent(link.hash.slice(1)); }
      catch(error){ return link.getAttribute('href').slice(1); }
    }
    var targets = links.map(function(link){ return document.getElementById(targetId(link)); }).filter(Boolean);
    var observer = new IntersectionObserver(function(entries){
      var visible = entries.filter(function(entry){ return entry.isIntersecting; }).sort(function(a,b){ return a.boundingClientRect.top - b.boundingClientRect.top; })[0];
      if(!visible) return;
      links.forEach(function(link){
        var current = targetId(link) === visible.target.id;
        link.classList.toggle('active', current);
        if(current) link.setAttribute('aria-current','location');
        else link.removeAttribute('aria-current');
      });
    }, { rootMargin: '-18% 0px -70% 0px' });
    targets.forEach(function(target){ observer.observe(target); });
  }
})();
