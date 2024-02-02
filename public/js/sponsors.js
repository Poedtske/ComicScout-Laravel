let main= document.querySelector("body main");
// async function asyncCall(){
//   let date
//   await fetch("./Sponsor.json")
//   .then((response)=>response.json())
//   .then((data)=>date=data);
//   console.log('hi');
//   console.log(date);
//   return date;

//   let response= await fetch("./Sponsor.json")
//   let promise= await response.json().then((data)=>data=data);
//   return promise;

// }
// asyncCall():

// console.log(h);
// let loadJson= new Promise(asyncCall(resolve,reject){
//   resolve
// });
// console.log('no');;
// console.log(sponsors);

const shuffle = (array) => {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
};

async function asyncCall() {
  try {
    const response = await fetch("./Sponsor.json");
    let sponsors = await response.json();
    let sponsorsLength=sponsors.length;



    const ranking =(array,rang)=>{
      for(let i=0;i<sponsors.length;i++){
        if(sponsors[i].rang==rang){

          array.push(sponsors[i]);
          sponsors.splice(i,1);
          console.log(rang);
          i--;
        }
      }
    }
    let rang2=[];
    let rang3=[];
    let rang4=[];

    ranking(rang2,2);
    ranking(rang3,3);
    ranking(rang4,4);



    shuffle(rang2);
    shuffle(rang3);
    shuffle(rang4);



    sponsors=[...sponsors, ...rang2,...rang3,...rang4];
    sponsors.length=sponsorsLength;


    const placingImage = (sponsor) => {
      if (sponsor.link == null || sponsor.naam == null || sponsor.logo == null||sponsor.rang==null) {
        throw new Error('Incomplete sponsor data');
      }

      let width=200;
      // let background=`background-color:black;`;

      if(sponsor.rang==1){
        width =290;
      }
      else if(sponsor.rang==2){
        width=250;
        // background=`background-color:white;`;
      }
      else if(sponsor.rang==3){
        width=220;
        // background=`background-color:gray;`;
      }


      const linkElement = sponsor.link == '#' ?
        `<button class="no_website"  style="width: ${width}px;">
          <img src="../images/${sponsor.logo}" alt="${sponsor.naam} style="width: ${width}px;"">
        </button>` :
        `<button style="width: ${width}px;">
          <a href="${sponsor.link}" target="_blank">
            <img src="../images/${sponsor.logo}" alt="${sponsor.naam} style="width: ${width}px;"">
          </a>
        </button>`;

      return `<section>${linkElement}</section>`;
    };

    sponsors.forEach(element => {
        console.log(element);
    });

    const sponsorElements = sponsors.map(placingImage).join('');
    main.innerHTML = sponsorElements;
  } catch (error) {
    console.error('Error:', error);
  }

}

asyncCall();


//lijst met sponsors
// const sponsors=



