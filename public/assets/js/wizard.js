$(function() {
  'use strict';

  $("#wizardVertical").steps({
    headerTag: "h2",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    stepsOrientation: 'vertical',
    onFinished: function (event, currentIndex) {
      $("#formHuruf").submit();
    }
  });

  $("#wizardEdit").steps({
    headerTag: "h2",
    bodyTag: "section",
    enableAllSteps: true,
    transitionEffect: "slideLeft",
    stepsOrientation: 'vertical',
    onFinished: function (event, currentIndex) {
      $("#formHuruf").submit();
    }
  });
});