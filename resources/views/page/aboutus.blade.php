@extends('layout.default')

@section('title')
	<title>About Us - {{ Config::get('other.title') }}</title>
@stop

@section('meta')
	<meta name="description" content="About Us">
@stop

@section('breadcrumb')
<li>
    <a href="{{ route('about') }}" itemprop="url" class="l-breadcrumb-item-link">
        <span itemprop="title" class="l-breadcrumb-item-link-title">About Us</span>
    </a>
</li>
@stop

@section('content')
<style>
@import url('https://fonts.googleapis.com/css?family=Shrikhand');

div.header {
  background-position: 0px 0px;
  background-color: #0a1526;
  position: relative;
  top: 0;
  left: 0;
}

div.header.gradient {
  width: 100%;
  background-image: radial-gradient(at 25% top, rgba(50, 17, 44, 1) 0%, rgba(10, 21, 38, 1) 40%);
  z-index: 0;
}

div.header div.inner_content {
  padding-top: 70px;
  box-sizing: border-box;
  width: 100%;
  background-image: url('https://www.themoviedb.org/assets/static_cache/1a73b46e90cbab1fdce552dff8f14aa3/images/v4/marketing/red_pipes.svg');
  background-position: center -250px;
  background-repeat: no-repeat;
  position: relative;
  top: 0;
  left: 0;
  z-index: 2;
}

div.header div.inner_content div.content {
  color: #fff;
  padding: 20px 0 70px 0;
  width: 800px;
  margin: 0 auto;
  text-align: center;
}

div.header div.inner_content div.content em {
  font-style: normal;
  border-bottom: 1px solid #d40242;
}

div.header div.inner_content div.content h2 {
  height: 233px;
  line-height: 233px;
  font-family: 'Shrikhand', cursive;
  font-size: 7em;
  font-weight: 400;
  position: relative;
  top: 0;
  left: 0;
}

div.header div.inner_content div.content h3 {
  font-size: 4em;
  margin-top: 40px;
  margin-bottom: 20px;
  font-weight: 700;
}

div.header div.inner_content div.content h4 {
  font-size: 2em;
  margin-top: 70px;
  margin-bottom: 40px;
  font-weight: 700;
}

div.header div.inner_content img {
  position: relative;
  top: 0;
  left: 0;
  margin-top: -140px;
  outline-style: hidden;
}

div.header div.inner_content div.content p {
  margin: 0 auto;
  text-align: center;
  font-size: 1.2em;
}

div.header div.inner_content div.wrapper {
  margin: 0 auto;
  width: 100%;
}

div.header div.inner_content div.wrapper div {
  width: 100%;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
}

div.header div.inner_content div.wrapper div p {
  text-align: left;
  margin: 0;
  width: 92vw;
}

div.header div.inner_content div.wrapper div > div {
  color: #d40242;
  font-weight: 700;
  font-size: 4em;
  line-height: 0.8em;
  opacity: 0.7;
  width: 8vw;
}

a.button {
  margin-top: 40px;
  border-radius: 5px;
  background-color: #d40242;
  display: inline-block;
  padding: 10px 20px;
  color: #fff;
  text-transform: uppercase;
  font-weight: 700;
  font-size: 1.2em;
  transition: 0.2s all;
}

a.button:hover {
  background-color: #0a1526;
  color: #d40242;
}

a.button.white {
  background-color: #fff;
  color: #d40242;
}

a.button.white:hover {
  background-color: #d40242;
  color: #fff;
}

div.padit_young_kenobi {
  padding: 64px 0;
}

div.customers {
  width: 100%;
  background-color: #fff;
}

div.pager {
  margin: 0 auto;
  height: 360px;
  width: 1100px;
  box-sizing: border-box;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  flex-wrap: wrap;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  justify-content: center;
}

div.arrow {
  color: #ccc;
  font-size: 2.2em;
  position: relative;
  top: 20px;
  transition: 0.2s all;
  box-sizing: border-box;
}

div.arrow {
  width: 50px;
}

div.arrow.right {
  text-align: right;
}

div.arrow:hover {
  color: #d40242;
  cursor: pointer;
}

div.pager_selector {
  padding-top: 20px;
  text-align: center;
  color: #ccc;
  width: 1000px;
}

div.pager_selector div.circle {
  background-color: #ccc;
  display: inline-block;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  margin: 0 4px;
  transition: 0.2s all;
}

div.pager_selector div.circle.on, div.pager_selector div.circle:hover {
  background-color: #d40242;
}

div.pager_selector div.circle:hover {
  cursor: pointer;
}

div.customers div.customer {
  width: 1100px;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  justify-content: space-between;
}

div.customers div.customer > img {
  box-sizing: border-box;
}

div.customers div.customer div.text {
  width: 640px;
  padding-left: 30px;
  box-sizing: border-box;
}

div.customers div.customer div.text p {
  font-size: 1.6em;
  font-style: italic;
  color: #333333;
}

div.customers div.customer div.text.smaller p {
  font-size: 1.3em;
}

div.customers div.customer div.text p.name {
  border-top: 1px solid #cccccc;
  padding-top: 10px;
  font-size: 1.3em;
  font-weight: 300;
}

div.apps {
  background-color: #eeeeee;
}

div.apps h3 {
  color: #333333;
  font-weight: 600;
  font-size: 1.6em;
  margin-bottom: 20px;
}

div.apps div.content {
  width: 1000px;
  margin: 0 auto;
}

div.apps div.content div.cards {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  flex-wrap: wrap;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  justify-content: space-between;
  box-sizing: border-box;
}

div.apps div.content div.cards + h3 {
  margin-top: 70px;
}

div.apps div.content div.card {
  width: 320px;
  height: 234px;
  color: #fff;
  overflow: hidden;
}

div.apps div.content div.card div.title {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  flex-wrap: wrap;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  box-sizing: border-box;
  width: 100%;
  height: 132px;
  padding: 16px;
}

div.apps div.content div.card div.info {
  width: 100%;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-align-items: flex-end;
  -moz-align-items: flex-end;
  -ms-align-items: flex-end;
  -ms-flex-align: flex-end;
  align-items: flex-end;
}

div.apps div.content div.card div.info div.initials {
  width: 90px;
  height: 90px;
  box-sizing: border-box;
  border: 1px solid #fff;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 5px;
  color: #fff;
  font-size: 2.8em;
  line-height: 86px;
  text-transform: uppercase;
  text-align: center;
}


div.apps div.content div.card div.title div.text {
  box-sizing: border-box;
  padding-left: 16px;
  max-width: 198px;
}

div.apps div.content div.card div.description {
  box-sizing: border-box;
  padding: 16px;
  color: #000;
  font-size: 1em;
}

div.apps div.content div.card div.description > * {
  display: none;
}

div.apps div.content div.card div.description > *:first-child {
  display: block;
}

div.apps div.content div.card div.text h2, div.apps div.content div.card div.text p, div.apps div.content div.card div.text a {
  margin-bottom: 0;
  color: #fff;
}

div.apps div.content div.card div.text h2, div.apps div.content div.card div.text p {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

 div.apps div.content div.card div.description p {
  height: 4.2em;
  color: #000;
  font-size: 1em;
  line-height: 1.4em;
  font-weight: 300;
  margin: 0;
}

div.apps div.content div.cards.small div.card {
  width: 233px;
  height: 66px;
}

div.apps div.content div.cards.small div.title {
  width: 100%;
  height: 66px;
  padding: 8px;
}

div.apps div.content div.cards.small div.card h2 {
  font-size: 1.2em;
}

div.apps div.content div.cards.small div.text h2, div.apps div.content div.cards.small div.text p, div.apps div.content div.cards.small div.text a {
  color: #333333;
}

div.apps div.content div.cards.small div.text h2 a {
  font-weight: 400;
}

div.apps div.content div.cards.small div.text p {
  font-weight: 300;
}

div.centered {
  text-align: center;
}

div.stats {
  padding: 64px 0;
  background-color: #fff;
}

div.stats div.content {
  width: 1000px;
  margin: 0 auto;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  flex-wrap: wrap;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  justify-content: space-between;
}

div.stats div.content h2 {
  font-size: 2em;
  color: #0a1526;
}

div.stats div.content div.inner_stats {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  flex-wrap: wrap;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -ms-flex-align: center;
  justify-content: space-between;
  width: 679px;
  padding-right: 60px;
  box-sizing: border-box;
}

div.stats div.content div.inner_stats div.stat p {
  margin-bottom: 0;
  font-weight: 300;
}

div.stats div.content div.inner_stats div.stat p:first-of-type {
  color: #0a1526;
  font-size: 1.6em;
  font-weight: 600;
}
</style>

<div class="container">
  <div class="block">
    <div class="header gradient">
      <div class="inner_content">
        <div class="content">
          <h2>Hey There</h2>
          <img src="{{ url('img/deadpool.png') }}" width="902" height="298">

          <h3>Let's Talk About Blutopia</h3>
          <p>Blutopia (BLU) is a <em>community-built</em> Movie/TV/FANRES database. Every piece of data has been added by our amazing community since 2017. Blutopia's strong <em>focus</em>&nbsp;is on HD content, a proactive userbase, an awesome/secure
            codebase and a helpful and friendly Staff team.</p>

          <h4><i class="fa fa-globe" aria-hidden="true"></i> The Blutopian Advantage <i class="fa fa-globe" aria-hidden="true"></i></h4>
          <div class="wrapper">
            <div>
              <div>1</div>
              <p>We have experienced members and staff that are well versed in the world of HD video / audio and technical support.</p>
            </div>

            <div>
              <div>2</div>
              <p>Along with our extensive passion for movies and TV shows, we also offer one of the best selections of something that most don't - FANRES! <em>A BIG THANK YOU</em> to our content bringers.</p>
            </div>

            <div>
              <div>3</div>
              <p>We don't accept donations to keep the site up and running. We feel that is our responsibility. That means no pestering PM's or banners on site. No begging from us.</p>
            </div>

            <div>
              <div>4</div>
              <p>Our community is second to none for its early age. Between our staff and userbase, we're always here to help. We're passionate about making sure your experience on Blutopia is nothing short of amazing.</p>
            </div>

            <div>
              <div>5</div>
              <p>Our service is used daily by many people across the globe. We've proven that we care about the functionality and security of our codebase and it can be trusted and relied on. Our developers work daily to provide a truly nex-gen codebase.</p>
            </div>


            <h4><i class="fa fa-globe" aria-hidden="true"></i> What We Need From You <i class="fa fa-globe" aria-hidden="true"></i></h4>
            <div>
              <div>1</div>
              <p>To be an active member of the community! This means to join in conversations productively, add approved content and help other users if you are able. </p>
            </div>

            <div>
              <div>2</div>
              <p>To read the rules in full and please respect them!</p>
            </div>

            <div>
              <div>3</div>
              <p>Make suggestions! We are striving to make Blutopia better each day. We aren't saying that every suggestion will be used, but it never hurts to see new ideas.</p>
            </div>
          </div>

          <a href="{{ route('contact') }}" class="contact button white">Contact Blutopia</a>
        </div>
      </div>
    </div>
  </div>
</div>
@stop
