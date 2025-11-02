(function(){
  function norm(s){return (s||'').replace(/\r\n/g,'\n').replace(/[“”]/g,'"').replace(/[‘’]/g,"'");}
  function getPrompt(btn){
    return norm(btn?.dataset?.prompt || btn.closest('.pl-card')?.dataset?.prompt || '');
  }
  function insertIntoChat(text){
    const ta = document.querySelector('.mwai-chatbot textarea, #mwai_chatbot textarea, .mwai-input textarea');
    if(!ta) return false;
    ta.focus(); ta.value = text;
    ta.dispatchEvent(new Event('input',{bubbles:true}));
    ta.dispatchEvent(new Event('change',{bubbles:true}));
    return true;
  }
  document.addEventListener('click',function(e){
    const btn = e.target.closest('.pl-use-prompt');
    if(!btn) return;
    e.preventDefault();
    const text = getPrompt(btn);
    if(!text){ console.warn('[PL] Empty data-prompt; check meta key pl_prompt_text.'); return; }
    if(!insertIntoChat(text)){ navigator.clipboard?.writeText(text).catch(()=>{}); }
  });
})();
