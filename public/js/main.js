const hamburgerBtn = document.querySelector("header button.hamburger");
const nav = document.querySelector("header nav");

// Initialize aria-expanded to false on page load
window.onload = () => {
  hamburgerBtn.setAttribute("aria-expanded", "false");
};
const invertAria = (el, attr) => {
  console.log(el, attr);
  console.log("el.attribute: " + el.getAttribute(`aria-${attr}`));
  el.setAttribute(`aria-${attr}`, el.getAttribute(`aria-${attr}`) !== "true");
  console.log("el.attribute: " + el.getAttribute(`aria-${attr}`));
};

let convertRemToPixels=(rem) =>{
  return rem * parseFloat(getComputedStyle(document.documentElement).fontSize);
}



const isMobile = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
const eventToUse = isMobile ? "touchend" : "click";

if (isMobile) {
  console.log("Mobile device detected");
} else {
  console.log("Desktop device detected");
}

hamburgerBtn.addEventListener(
  eventToUse,
  () => {
    console.log(eventToUse);
    [hamburgerBtn, nav].forEach((el) => invertAria(el, "expanded"));
  },
  { passive: true }
);




// // let docName=document.getElementsByTagName('TITLE')[0];
// const hamburgerBtn = document.querySelector("header button.hamburger");
// const nav = document.querySelector("header nav");
// const navBtn=document.querySelector("header nav div.dropdown");

// window.onload=()=>{
//   let a=document.getElementsByClassName('hamburger');
//   a.setAttribute(`aria-expanded`)=false;
// }
// // if (docName=='Home'||docName=='Fotos'||docName=='Sponsors')
// // {
// //     let head=document.getElementsByTagName('HEAD')[0];
// //     let link = document.createElement('link');
// //     link.rel = 'stylesheet';
// //     link.type = 'text/css';
// //     link.href = '../main.js';
// //     head.appendChild(link);
// // }
// // else{
// //     let head=document.getElementsByTagName('HEAD')[0];
// //     let link = document.createElement('link');
// //     link.rel = 'stylesheet';
// //     link.type = 'text/css';
// //     link.href = '../../main.css';
// //     head.appendChild(link);
// // }



// const invertAria = (el, attr) => {
//   console.log(el, attr);
//   console.log("el.attribute: "+el.getAttribute(`aria-${attr}`));
//   el.setAttribute(`aria-${attr}`, el.getAttribute(`aria-${attr}`) !== "true");
//   console.log("el.attribute: "+el.getAttribute(`aria-${attr}`));
// };

// hamburgerBtn.addEventListener(
//   "click",
//   () => {
//     console.log("clicked");
//     [hamburgerBtn, nav].forEach((el) => invertAria(el, "expanded"));
//   },
//   { passive: true }
// );

// hamburgerBtn.addEventListener(
//   "touchend",
//   () => {
//     console.log("clicked");
//     [hamburgerBtn, nav].forEach((el) => invertAria(el, "expanded"));
//   },
//   { passive: true }
// );

// let evt=document.createEvent("HTMLEvents");
// evt.initEvent("click",true,true);
// document.getElementsByClassName('hamburger').dispatchEvent(evt);




