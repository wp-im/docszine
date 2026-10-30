(function(){
  'use strict';
  var links = Array.prototype.slice.call(document.querySelectorAll('.docszine-toc a[href^="#"]'));
  if(links.length && 'IntersectionObserver' in window){
    var targets = links.map(function(link){ return document.querySelector(link.getAttribute('href')); }).filter(Boolean);
    var observer = new IntersectionObserver(function(entries){
      var visible = entries.filter(function(entry){ return entry.isIntersecting; }).sort(function(a,b){ return a.boundingClientRect.top - b.boundingClientRect.top; })[0];
      if(!visible) return;
      links.forEach(function(link){ link.classList.toggle('active', link.getAttribute('href') === '#' + visible.target.id); });
    }, { rootMargin: '-18% 0px -70% 0px' });
    targets.forEach(function(target){ observer.observe(target); });
  }
})();
