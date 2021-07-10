$(function() {
  'use strict';

  // $('textarea').each(element => {
  //   var simplemde = new SimpleMDE({
  //     toolbar: [
  //       "bold", "italic",
  //       "heading", "heading-smaller", "heading-bigger", "|", 
  //       "quote", "unordered-list", "ordered-list", "table", "|",
  //       "preview", "guide"],
  //     shortcuts: {                
  //         "toggleFullScreen": null,
  //         "toggleSideBySide": null,   "heading-smaller", "heading-bigger",          
  //     },
  //     element: element,
  //   });
  // });
  
  [].forEach.call(document.getElementsByClassName('textEditor'),element => {
    const simplemde = new SimpleMDE({
      toolbar: [
        "bold", "italic",
        "heading",  "|", 
        "unordered-list", "ordered-list", "table", "|",
        "preview", "guide"],
      shortcuts: {                
          "toggleFullScreen": null,
          "toggleSideBySide": null,             
      },
      element: element,
    });
  });
});

