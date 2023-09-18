// js/relational.js
// https://nortonb.web582.com/relational_db/

checkLogin();
let movieSearch=[];
let movieExists = false;

/** display data section  */
  async function fetchFavourites(url){
    const repsonse = await fetch(url);
    const data = await repsonse.json();
    //console.log(data);
    displayData(data);
  }

  //call function to fetch data
  fetchFavourites('app/select.php');

  function displayData(data){
    //select element from HTML where we'll put our tv show
    const display = document.querySelector('#display');
    display.innerHTML = '';

    //create an unordered list
    let ul = document.createElement('ul');
    let a = document.createElement('a');

    data.forEach((movie)=>{
      movieSearch.push(movie.movieTitle);
      let li = document.createElement('li');
      li.innerHTML = `${movie.movieTitle} :: ${movie.otherInfo} and has ${movie.countedFavourites} favourite(s).`;
      li.appendChild(a);
      ul.appendChild(li);
    })
    display.appendChild(ul);
    //console.log(movieSearch);
  }

// ** login section ** //
  // check if logged in
  async function checkLogin(){
    let url = 'app/login_check.php';
    const response = await fetch(url);
    const confirmation = await response.json();
    //console.log(confirmation[0].verify);
    console.log(confirmation);
    if(!confirmation[0].verify){
      loadLoginForm();
    }else{
      loadFavouritesForm();
    }
    //call function again to refresh the page
    //fetchFavourites('app/select.php');
  }

// ** dynamically loaded content ** //
  //for dynamically loaded content, we need to addEventListener on the parent, then delegate to the children
  const forms = document.querySelector('#forms');
  forms.addEventListener('keyup',(event)=>{
    //console.log(event.target.id);
    if(event.target.id == "movie"){
      typeSearch(event);
    }
  })
  forms.addEventListener('click',(event)=>{
    //console.log(event.target.id);
    if(event.target.id == "submit-button"){
      getFormData(event);
    }
  })

    async function loadLoginForm(){
      let url = 'login_form.html';
      const response = await fetch(url);
      const loginform = await response.text();
      //console.log(loginform);
      forms.innerHTML = loginform;
      const loginButton = document.querySelector('#login-button');
      loginButton.addEventListener('click', login);
    }

    async function loadFavouritesForm(){
      let url = 'favourites_form.html';
      const response = await fetch(url);
      const favouritesform = await response.text();
      //console.log(favouritesform);
      forms.innerHTML = favouritesform;
      //then add queries & event listeners
      typeSearch();
    }

    function login(event){
      event.preventDefault();
      //get the form data & call an async function
      const loginFormData = new FormData(document.querySelector('#input-form'));
      loginUser(loginFormData);
    }

    async function loginUser(data){
      let url = 'app/login.php';
      const response = await fetch(url, {
        method: "POST",
        body: data
      });
      const confirmation = await response.json();
      console.log(confirmation);
      if(confirmation[1].verify == true){
        loadFavouritesForm();
      }
    }


  // ** logout user / remove verify from session *//
    const logoutButton = document.querySelector('#logout');
    logoutButton.addEventListener('click', logout);
    
    async function logout(){
      let url = 'app/logout.php';
      const response = await fetch(url);
      const confirmation = await response.json();
      console.log(confirmation);
      checkLogin();
    }

/** insert section  **/
  //inserting into 3 tables, movies, genres & favourites

  function getFormData(event){
    event.preventDefault();

    //get the form data & call an async function
    const insertFormData = new FormData(document.querySelector('#input-form'));
    url = 'app/insert_favourite.php';
    inserter(insertFormData, url);
  }

  async function inserter(data, url){
    const response = await fetch(url, {
      method: "POST",
      body: data
    });
    const confirmation = await response.json();
    console.log(confirmation);
    //call function again to refresh the page
    fetchFavourites('app/select.php');
  }


//search on keyup in movie title
// based on Tanveer's Pokemon API https://sktanveer65.web582.com/dynamic-web-prog/pokemonAPI/pokemon.html
function typeSearch(event){
  const movieInput = document.querySelector('#movie');
    let result = [];
    let input = movieInput.value;
    if(input.length){
        result = movieSearch.filter((keyword) => {
            return keyword.toLowerCase().includes(input.toLowerCase());
        }); 
        //console.log(result); 
    }
    display(result);
}

function display(result){
  const resultsBox = document.querySelector('#results-box');
  const content = result.map((list) => {
    //not thrilled with using "onlclick"
    //return "<li onclick=selectInput(this)>" + list + "</li>";
    return `<li onclick=selectInput(this)> ${list}</li>`; //updated to use template literals
  });
  if(!result.length){
    resultsBox.innerHTML = '';  
  }else{
    //resultsBox.innerHTML = "<ul>" + content.join('') + "</ul>";
    resultsBox.innerHTML = `<ul> ${content.join('')}</ul>`;
  }
}

function selectInput(list){
  movieExists = true;
  console.log(movieExists);
  document.querySelector('#movie').value = list.innerHTML;
  document.querySelector('#results-box').innerHTML = '';
}
