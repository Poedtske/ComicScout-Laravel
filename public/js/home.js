'use strict'
console.log("Connected");


{{  }}

let activityList=[
  {
      "naam":"11 novemberviering",
      "datum":"11/11/2023",
      "uur":"10:00"
  },
  {
      "naam":"Sint Ceciliafeest",
      "datum":"18/11/2023",
      "uur":"12:00"
  },
  {
    "naam":"Kerstconcert",
    "datum":"02/12/2023",
    "uur":"20:00"
  },
  {
    "naam":"Kerstweekend: Kerstconcert",
    "datum":"16/12/2023",
    "uur":"19:00"
  },
  {
    "naam":"Kerstweekend: Concert De vierde wijze",
    "datum":"17/12/2023",
    "uur":"18:30"
  },
  // {
  //     "naam":"",
  //     "datum":"",
  //     "uur":""
  // }
]

let currentImageIndex=0;
// const pNaam = ;
const pDatum = document.getElementById("datum");
const pUur = document.getElementById("uur");
let hasBeenClicked=false;
let slideInterval;


let assignValues=()=>{
  document.getElementById("naam").innerHTML= activityList[currentImageIndex].naam;
  document.getElementById("datum").innerHTML= activityList[currentImageIndex].datum;
  document.getElementById("uur").innerHTML=activityList[currentImageIndex].uur;
}

// let getValues=()=>{
//   console.log(activityList[currentImageIndex].naam);
//   console.log(activityList[currentImageIndex].datum);
//   console.log(activityList[currentImageIndex].uur);
// }
assignValues();
let nextActivity= () => {
  console.log("next");


  if(currentImageIndex==activityList.length-1){
    currentImageIndex=0;
  }
  else{
    currentImageIndex++
  }
  //getValues();
  assignValues();
}

let prevActivity= () => {
  console.log("prev");


  if(currentImageIndex==0){
    currentImageIndex=activityList.length-1;
  }
  else{
    currentImageIndex--;
  }
  //getValues();
  assignValues();

}

let clicked=()=>{
  hasBeenClicked=true;
  console.log("clicked");
  clearInterval(slideInterval);
}

const next=document.getElementById("next");
const prev=document.getElementById("prev");
console.log(next);

slideInterval=setInterval(nextActivity, 5000);
next.addEventListener("click", nextActivity, clicked);
next.addEventListener("click", clicked);
prev.addEventListener("click", prevActivity, clicked);
prev.addEventListener("click", clicked);
