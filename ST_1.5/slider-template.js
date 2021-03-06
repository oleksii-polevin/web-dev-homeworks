const API_URL = 'https://picsum.photos/';
const BIG_SIZE = '600/400';
const SMALL_SIZE = '60';

const IMAGES = [
  '?image=1080',
  '?image=1079',
  '?image=1069',
  '?image=1063',
  '?image=1050',
  '?image=1039'
];

//main image
const bigImg = $('.slider-current').children(":first");

// initialise variable for preview picture
let current;

$(document).ready(function() {
  createPreview();
  current = $('.imgSmall').first().addClass('current');
});

const createPreview = () => {
  const preview = $('.slider-previews');
  let num = 0; // just for consistency
  IMAGES.forEach(item => {
    const img = $('<img/>', {
      src: API_URL + SMALL_SIZE + item,
      alt: num
    });
    num++;
    const li = $('<li></li>').addClass('imgSmall').append(img);
    preview.append(li);
  });
};

// select img on click
$('.slider-previews').click(function(e) {
  // react only on images
  if ($(e.target).is('img')) {
    current.removeClass('current');
    current = $(e.target).closest('li').addClass('current');
    slider(e.target);
  }
});

// select img from keyboard
$(document).keydown(function(e) {
  if (e.keyCode === 39) { // 39 - right 37 - left
    keyReact(current.next(), $('li').first());
  }
  else if (e.keyCode === 37) {
    keyReact(current.prev(), $('li').last());
  }
});

/* changes big image
 element = chosen small img
 */
const slider = element => {
  const src = element.src.replace(SMALL_SIZE, BIG_SIZE);
  bigImg.attr('src', src);
};

// checks following or preceding preview img existence
const isPresent = element => {
  return element.length > 0;
};

// processing reaction on '<' and '>' keys
const keyReact = (followingElem, edgeElem) => {
  current.removeClass('current');
  isPresent(followingElem)
  ? current = followingElem.addClass('current')
  : current = edgeElem.addClass('current');
  slider(current.find('img')[0]);
};
