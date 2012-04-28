CREATE TABLE category (
	category_id INT(3) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	category_name VARCHAR(30)
);

INSERT INTO category VALUES (1,"Blogs");
INSERT INTO category VALUES (2,"Business");
INSERT INTO category VALUES (3,"Computers");
INSERT INTO category VALUES (4,"Entertainment");
INSERT INTO category VALUES (5,"Humanities");
INSERT INTO category VALUES (6,"Health");
INSERT INTO category VALUES (7,"News");
INSERT INTO category VALUES (8,"Podcasts");  
INSERT INTO category VALUES (9,"Politics");
INSERT INTO category VALUES (10,"Science");
INSERT INTO category VALUES (11,"Sports");
INSERT INTO category VALUES (12,"Travel");

CREATE TABLE `subcategory` (
`category_id` INT( 3 ) NOT NULL ,
`subcategory_id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`subcategory_name` VARCHAR( 30 ) NOT NULL ,
INDEX ( `category_id` ) ,
FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

INSERT INTO subcategory VALUES (1, 1, "Community");
INSERT INTO subcategory VALUES (1, 2, "Corporate");
INSERT INTO subcategory VALUES (1, 3, "Personal");
INSERT INTO subcategory VALUES (2, 4, "Economics");
INSERT INTO subcategory VALUES (2, 5, "Entrepreneurship");
INSERT INTO subcategory VALUES (2, 6, "Industry");
INSERT INTO subcategory VALUES (2, 7, "Investor Relations");
INSERT INTO subcategory VALUES (2, 8, "Law");
INSERT INTO subcategory VALUES (2, 9, "Products");
INSERT INTO subcategory VALUES (2, 10, "Services");
INSERT INTO subcategory VALUES (2, 11, "Stock Market");
INSERT INTO subcategory VALUES (3, 12, "Hardware");
INSERT INTO subcategory VALUES (3, 13, "Internet");
INSERT INTO subcategory VALUES (3, 14, "Linux");
INSERT INTO subcategory VALUES (3, 15, "Mac");
INSERT INTO subcategory VALUES (3, 16, "Mobile");
INSERT INTO subcategory VALUES (3, 17, "Programming Languages");
INSERT INTO subcategory VALUES (3, 18, "Security");  
INSERT INTO subcategory VALUES (3, 19, "Software");
INSERT INTO subcategory VALUES (3, 20, "Technology");
INSERT INTO subcategory VALUES (3, 21, "Windows");
INSERT INTO subcategory VALUES (3, 22, "Wireless");
INSERT INTO subcategory VALUES (4, 23, "Arts");
INSERT INTO subcategory VALUES (4, 24, "Books");
INSERT INTO subcategory VALUES (4, 25, "Cars");
INSERT INTO subcategory VALUES (4, 26, "Comics");
INSERT INTO subcategory VALUES (4, 27, "Dance");
INSERT INTO subcategory VALUES (4, 28, "Games");
INSERT INTO subcategory VALUES (4, 29, "Humor");
INSERT INTO subcategory VALUES (4, 30, "Movies");
INSERT INTO subcategory VALUES (4, 31, "Music");
INSERT INTO subcategory VALUES (4, 32, "Quotes");
INSERT INTO subcategory VALUES (4, 33, "Science Fiction");
INSERT INTO subcategory VALUES (4, 34, "TV");  
INSERT INTO subcategory VALUES (5, 35, "History");
INSERT INTO subcategory VALUES (5, 36, "Language and Linguistics");
INSERT INTO subcategory VALUES (5, 37, "Philosophy");
INSERT INTO subcategory VALUES (5, 38, "Religion");
INSERT INTO subcategory VALUES (5, 39, "Self-Help");
INSERT INTO subcategory VALUES (5, 40, "Spirituality");
INSERT INTO subcategory VALUES (6, 41, "Diets and Nutrition");
INSERT INTO subcategory VALUES (6, 42, "Exercise");  
INSERT INTO subcategory VALUES (6, 43, "Recipes");
INSERT INTO subcategory VALUES (6, 44, "Outdoors Living");
INSERT INTO subcategory VALUES (7, 45, "Conspiracy");
INSERT INTO subcategory VALUES (7, 47, "Magazine");
INSERT INTO subcategory VALUES (7, 48, "Tabloid");
INSERT INTO subcategory VALUES (7, 49, "War");
INSERT INTO subcategory VALUES (8, 50, "E-Learning Lesson");
INSERT INTO subcategory VALUES (8, 51, "Interview");
INSERT INTO subcategory VALUES (8, 52, "Vodcasts");
INSERT INTO subcategory VALUES (9, 53, "Civics");
INSERT INTO subcategory VALUES (9, 54, "Embassies");
INSERT INTO subcategory VALUES (9, 55, "International Relations");
INSERT INTO subcategory VALUES (9, 56, "Peace Studies");
INSERT INTO subcategory VALUES (9, 57, "Policy");
INSERT INTO subcategory VALUES (9, 58, "Voting and Elections");
INSERT INTO subcategory VALUES (10, 59, "Architecture");
INSERT INTO subcategory VALUES (10, 60, "Archaeology");
INSERT INTO subcategory VALUES (10, 61, "Astronomy");
INSERT INTO subcategory VALUES (10, 62, "Biology");
INSERT INTO subcategory VALUES (10, 63, "Botany");
INSERT INTO subcategory VALUES (10, 64, "Chemistry");
INSERT INTO subcategory VALUES (10, 65, "Geology");
INSERT INTO subcategory VALUES (10, 66, "Engineering");
INSERT INTO subcategory VALUES (10, 67, "Environment");
INSERT INTO subcategory VALUES (10, 68, "Geography");
INSERT INTO subcategory VALUES (10, 69, "Mathematics");
INSERT INTO subcategory VALUES (10, 70, "Physics");
INSERT INTO subcategory VALUES (10, 71, "Psychology"); 
INSERT INTO subcategory VALUES (10, 72, "Sociology");
INSERT INTO subcategory VALUES (10, 73, "Statistics");
INSERT INTO subcategory VALUES (10, 74, "Zoology");
INSERT INTO subcategory VALUES (11, 75, "Baseball");
INSERT INTO subcategory VALUES (11, 76, "Basketball");
INSERT INTO subcategory VALUES (11, 77, "Cricket");
INSERT INTO subcategory VALUES (11, 78, "Football");
INSERT INTO subcategory VALUES (11, 79,"Headline");
INSERT INTO subcategory VALUES (11, 80,"Hockey");
INSERT INTO subcategory VALUES (11, 81,"Martial Arts");
INSERT INTO subcategory VALUES (11, 82,"Racing");
INSERT INTO subcategory VALUES (11, 83,"Rugby");
INSERT INTO subcategory VALUES (11, 84,"Soccer");
INSERT INTO subcategory VALUES (11, 85,"Track and Field");
INSERT INTO subcategory VALUES (11, 86,"Winter sports");
INSERT INTO subcategory VALUES (11, 87,"Water sports");
INSERT INTO subcategory VALUES (12, 88, "Adventure");  
INSERT INTO subcategory VALUES (12, 89, "Culture");
INSERT INTO subcategory VALUES (12, 90, "Nature");  
INSERT INTO subcategory VALUES (12, 91, "Survivalism");
INSERT INTO subcategory VALUES (12, 92, "Tickets and Deals");

CREATE TABLE news (
	news_id BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	news_image_id BIGINT(20), 
	news_title VARCHAR(50), 
	news_link VARCHAR(100), 
	news_description VARCHAR(150),  
	news_feed VARCHAR(255), 
	news_last_updated TIMESTAMP, 
    news_subcategory_id INT(3)
);  
  
-- Blogs (weblogs) --  
  --- INSERT INTO subcategory VALUES ("Community") ---
  INSERT INTO news VALUES (0, "RSSOwl News", "http://www.rssowl.org/newsfeed", "http://www.rssowl.org", 1);
  INSERT INTO news VALUES (0, "HorsePigCow", "http://feeds.feedburner.com/horsepigcowLifeUncommon", "http://www.horsepigcow.com", "...helping you find a cure for 'viral'...", 1);
  INSERT INTO news VALUES (0, "Lifehacker", "http://www.lifehacker.com/index.xml", "http://lifehacker.com", "Computers make us more productive. Yeah, right. Lifehacker recommends the software downloads and web sites that actually save time. Don't live to geek; geek to live.", 1);
  --- INSERT INTO subcategory VALUES ("Corporate") ---
  INSERT INTO news VALUES (0, "Google Blogoscoped", "http://blog.outer-court.com/rss.xml", "http://blogoscoped.com", "Google, the World, and the World Wide Web, Weblogged", 2);
  INSERT INTO news VALUES (0, "Google Sightseeing", "http://googlesightseeing.com/feed", "http://googlesightseeing.com", "Why bother seeing the world for real?", 2);
  INSERT INTO news VALUES (0, "Flickr Blog", "http://blog.flickr.net/en/feed/", "http://developer.yahoo.com/blogs/ydn/", 2);
  INSERT INTO news VALUES (0, "Microsoft Developer Network blogs", "http://blogs.msdn.com/b/mainfeed.aspx", "http://msdn.microsoft.com/", 2);
  INSERT INTO news VALUES (0, "Yahoo! Developer Blog", "http://feeds.developer.yahoo.net/YDNBlog", "http://developer.yahoo.com/blogs/ydn/", "Everything you ever wanted to know about developing with Yahoo!", 2);
  --- INSERT INTO subcategory VALUES ("Personal") ---
  INSERT INTO news VALUES (0, "Anil Dash", "http://www.dashes.com/anil/index.rdf", "http://dashes.com/anil/", "A Blog About Making Culture", 3);
  INSERT INTO news VALUES (0, "Boing Boing", "http://www.boingboing.net/index.rdf", "http://www.boingboing.net/", 3);
  INSERT INTO news VALUES (0, "Pandagon", "http://pandagon.net/index.php/site/rss_2.0/", "http://pandagon.net/index.php/site/index/", 3);
  INSERT INTO news VALUES (0, "Wizbang", "http://wizbangblog.com/index.rdf", "http://wizbangblog.com/", "Explosively Unique...", 3);
-- INSERT INTO category VALUES ("Business"); --
  --- INSERT INTO subcategory VALUES ("Economics") ---
  	INSERT INTO news VALUES (0, "Smartmoney.com", "http://www.smartmoney.com/rss/smheadlines.cfm?feed=1&amp;format=rss091", "http://www.smartmoney.com/?cid=1122", "Investing, Saving and Personal Finance", 4);
  --- INSERT INTO subcategory VALUES ("Entrepreneurship") ---
	INSERT INTO news VALUES (0, "Fast Company", "http://www.fastcompany.com/rss.xml", "http://www.fastcompany.com", 5);
	INSERT INTO news VALUES (0, "I Will Teach You To Be Rich", "http://www.iwillteachyoutoberich.com/atom.xml", "http://www.iwillteachyoutoberich.com", "Personal finance blog for college students, recent graduates and everyone else -- including entrepreneurship -- for getting rich. Featured in the Wall Street Journal and New York Times.", 5);
	INSERT INTO news VALUES (0, "Inc.com", "http://www.inc.com/rss.xml", "http://www.inc.com", "Inc.com, the daily resource for entrepreneurs.", 5);
  --- INSERT INTO subcategory VALUES ("Industry") ---    	
   	INSERT INTO news VALUES (0, "NYT &gt; Business", "http://www.nytimes.com/services/xml/rss/nyt/Business.xml", "http://www.nytimes.com/pages/business/index.html?partner=rss", 6);
    INSERT INTO news VALUES (0, "WSJ.com: US Business", "http://online.wsj.com/xml/rss/0,,3_7014,00.xml", "http://online.wsj.com", "US Business", 6);
 --- INSERT INTO subcategory VALUES ("Investor Relations") ---
	INSERT INTO news VALUES (0, "Moneycontrol Top Headlines", "http://moneycontrol.com/rss/latestnews.xml", "http://www.moneycontrol.com", "Latest News from Moneycontrol.com", 7);
 --- INSERT INTO subcategory VALUES ("Law") ---
	INSERT INTO news VALUES (0, "Law.com - Newswire", "http://www.law.com/rss/rss_newswire.xml", "http://www.law.com/newswire/", "The day's top legal stories accompanied with summaries.", 8);
	INSERT INTO news VALUES (0, "Legal Blog Watch", "http://legalblogwatch.typepad.com/legal_blog_watch/atom.xml", "http://legalblogwatch.typepad.com/legal_blog_watch/", "Tap into the legal community's daily buzz with the e-mail version of Law.com's blog, Legal Blog Watch.", 8);
 --- INSERT INTO subcategory VALUES ("Products") ---
    INSERT INTO news VALUES (0, "Consumer Reports", "http://simplefeed.consumerreports.org/f/100003s2mdsbc00e6vf.rss", "http://www.consumerreports.org", "Consumer Reports: Expert product reviews and product Ratings", 9);
 --- INSERT INTO subcategory VALUES ("Services") --- 
    INSERT INTO news VALUES (0, "Yelp Local Service reviews", "http://www.yelp.com/syndicate/area/rss.xml?loc=New+York%2C+NY&category=localservices", "http://www.yelp.com/", "Yelp - connecting people with great local businesses", 10);
 --- INSERT INTO subcategory VALUES ("Stock Market") ---  
	INSERT INTO news VALUES (0, "MarketWatch", "http://feeds.marketwatch.com/marketwatch/topstories", "http://www.marketwatch.com/", "MarketWatch, a leading publisher of business and financial news, offers users up-to-the minute news, investment tools, and subscription products.", 11);
    INSERT INTO news VALUES (0, "The Motley Fool", "http://www.fool.com/xml/foolnews_rss091.xml", "http://www.fool.com/", "Today's top headlines from The Motley Fool", 11);
	INSERT INTO news VALUES (0, "TheStreet.com", "http://www.thestreet.com/feeds/rss/index.xml", "http://www.thestreet.com/", "All the latest stories from TheStreet.com's reporters and commentators covering stocks, personal finance, mutual funds, markets, and lifestyle &amp; leisure.", 11);    
-- INSERT INTO category VALUES ("Computers"); --
  --- INSERT INTO subcategory VALUES ("Hardware");  ---
    INSERT INTO news VALUES (0, "OS News", "http://www.osnews.com/files/recent.rdf", "http://www.osnews.com/", "Exploring the Future of Computing", 12);
    INSERT INTO news VALUES (0, "Engadget", "http://www.engadget.com/rss.xml", "http://www.engadget.com", "Engadget is a web magazine with obsessive daily coverage of everything new in gadgets and consumer electronics.", 12);
    INSERT INTO news VALUES (0, "Gizmodo", "http://www.gizmodo.com/index.xml", "http://gizmodo.com", "Gizmodo, the gadget guide. So much in love with shiny new toys, it's unnatural.", 12);
  --- INSERT INTO subcategory VALUES ("Internet"); ---
    INSERT INTO news VALUES (0, "456 Berea Street", "http://www.456bereastreet.com/feed.xml", "http://www.456bereastreet.com/", "Roger Johansson is a web professional specialising in web standards, accessibility, and usability.", 13);
    INSERT INTO news VALUES (0, "CSS Beauty News Feed", "http://www.cssbeauty.com/rss/news/", "http://www.cssbeauty.com/", "CSSBEAUTY is a project focused on providing its audience with a database of well designed CSS based websites from around the world.", 13);
    INSERT INTO news VALUES (0, "internetnews.com", "http://headlines.internet.com/internetnews/top-news/news.rss", "http://www.internetnews.com", "All the top news, features, analysis and insight into enterprise and Internet technology, geared for IT managers and delivered by the best in the industry.", 13);
    INSERT INTO news VALUES (0, "ReadWriteWeb", "http://www.readwriteweb.com/rss.xml", "http://www.readwriteweb.com/", 13); 
    INSERT INTO news VALUES (0, "SimpleBits", "http://www.simplebits.com/xml/rss.xml", "http://simplebits.com/", "Hand-crafted web sites, pixels and text by Dan Cederholm.", 13);
    INSERT INTO news VALUES (0, "Softpedia - Webmaster", "http://news.softpedia.com/newsRSS/Webmaster-4.xml", "http://news.softpedia.com", "Softpedia News - Webmaster", 13);
    INSERT INTO news VALUES (0, "TechCrunch", "http://feedproxy.google.com/Techcrunch", "http://www.techcrunch.com", "TechCrunch is a group-edited blog that profiles the companies, products and events defining and transforming the new web.", 13);
    INSERT INTO news VALUES (0, "WebDeveloper.com", "http://www.webdeveloper.com/webdeveloper.rdf", "http://www.webdeveloper.com", "Where Web Developers and Designers Learn how to Build Web Sites.", 13);
    INSERT INTO news VALUES (0, "WebReference News", "http://www.webreference.com/webreference.rdf", "http://www.webreference.com", "Daily news, views, and how-tos on all aspects of web design and development. Features free web-based tools, open source scripts, and in-depth tutorials on DHTML, HTML, JavaScript, 3D, Graphics, XML, and Design for webmasters.", 13);
    INSERT INTO news VALUES (0, "Webware.com", "http://www.webware.com/8300-1_109-2-0.xml", "http://www.webware.com/8300-17939_109-2.html", "Hands-on reviews and news about online software and new Web communities, from Webware.com.", 13);
    INSERT INTO news VALUES (0, "Wired Top Stories", "http://feeds.wired.com/wired/index", "http://www.wired.com/", "Wired.com - Top Stories", 13);
  ---  INSERT INTO subcategory VALUES ("Linux"); ---
    INSERT INTO news VALUES (0, "DesktopLinux.com", "http://www.desktoplinux.com/backend/headlines.rss", "http://www.desktoplinux.com/?kc=rss", "All About Linux on the Desktop", 14);
    INSERT INTO news VALUES (0, "DistroWatch.com: News", "http://distrowatch.com/news/dw.xml", "", "Latest news on Linux distributions and BSD projects", 14);
    INSERT INTO news VALUES (0, "Linux Journal", "http://www.linuxjournal.com/news.rss", "http://www.linuxjournal.com", "Since 1994: The Original Monthly Magazine of the Linux Community", 14);
    INSERT INTO news VALUES (0, "Linux Today", "http://linuxtoday.com/backend/biglt.rss", "http://linuxtoday.com", "Linux Today News Service", 14);
    INSERT INTO news VALUES (0, "Linux Weekly News", "http://lwn.net/headlines/rss", "http://lwn.net", "&#xA; LWN.net is a comprehensive source of news and opinions from&#xA; and about the Linux community.  This is the main LWN.net feed,&#xA; listing all articles which are posted to the site front page.", 14);
    INSERT INTO news VALUES (0, "LinuxInsider", "http://www.linuxinsider.com/perl/syndication/rssfull.pl", "http://www.linuxinsider.com", "LinuxInsider -- &quot;Linux News &amp; Information from Around the World&quot;", 14);
    INSERT INTO news VALUES (0, "Slashdot: Linux", "http://rss.slashdot.org/Slashdot/slashdotLinux", "", "News for nerds, stuff that matters", 14);
    INSERT INTO news VALUES (0, "Ubuntu Geek", "http://www.ubuntugeek.com/feed/", "http://www.ubuntugeek.com", "Ubuntu Linux Tutorials,Howtos,Tips &amp; News | Intrepid,Jaunty,Karmic", 14);
  --- INSERT INTO subcategory VALUES ("Mac"); ---
    INSERT INTO news VALUES (0, "Apple Hot News", "http://www.apple.com/main/rss/hotnews/hotnews.rss", "http://www.apple.com/hotnews/", "Hot News provided by Apple.", 15);
    INSERT INTO news VALUES (0, "AppleInsider", "http://www.appleinsider.com/appleinsider.rss", "http://www.appleinsider.com/", "AppleInsider has been the leading source of insider news and rumors on Apple Computer since 1997.", 15);
    INSERT INTO news VALUES (0, "digg - tech news / apple / dig", "http://digg.com/rss/indexappledig.xml", "http://digg.com/", "digg.com: Stories / Apple / Upcoming", 15);
    INSERT INTO news VALUES (0, "Google Mac Blog", "http://googlemac.blogspot.com/atom.xml", "http://googlemac.blogspot.com/", "Macs inside Google.", 15);
    INSERT INTO news VALUES (0, "Mac Help Forums", "http://www.mac-help.com/forums/external.php?type=rss2", "http://www.mac-help.com/forums/", "Mac OSX help and discussion forum website.", 15);
    INSERT INTO news VALUES (0, "Mac OS X Knowledge Base", "http://docs.info.apple.com/rss/macosx.rss", "http://www.apple.com/support/", "Apple - Support - Most Recent - Apple Inc.", 15);
    INSERT INTO news VALUES (0, "MACnn News", "http://macnn.com/macnn.rss", "http://www.macnn.com/", "MacNN is the leading source for news about Apple and the Mac industry. It offers news, reviews, discussion, tips, troubleshooting, links, and reviews every day. The best place for Mac News Period.", 15);
    INSERT INTO news VALUES (0, "macosxhints", "http://www.macosxhints.com/backend/geeklog.rdf", "http://www.macosxhints.com", "Macosxhints.com RSS feed", 15);
    INSERT INTO news VALUES (0, "MacRumors", "http://www.macrumors.com/macrumors.xml", "http://www.macrumors.com", "the mac news you care about", 15);
    INSERT INTO news VALUES (0, "Macworld", "http://www.macworld.com/rss.xml", "http://www.macworld.com", 15);
    INSERT INTO news VALUES (0, "Softpedia - Apple", "http://news.softpedia.com/newsRSS/Apple-8.xml", "http://news.softpedia.com", "Softpedia News - Apple", 15);
    INSERT INTO news VALUES (0, "The Unofficial Apple Weblog", "http://www.tuaw.com/rss.xml", "http://www.tuaw.com", "The Unofficial Apple Weblog (TUAW)", 15);
    INSERT INTO news VALUES (0, "VersionTracker: Mac OS X", "http://feeds.feedburner.com/versiontracker/macosx", "http://www.versiontracker.com/", "The #1 source for software updates", 15);
  --- INSERT INTO subcategory VALUES ("Mobile"); ---
    INSERT INTO news VALUES (0, "All About Symbian - News", "http://rss.allaboutsymbian.com/news/rss2all.xml", "http://www.allaboutsymbian.com/", "News Headlines from All About Symbian", 16);
    INSERT INTO news VALUES (0, "Brighthand.com", "http://www.brighthand.com/rss.xml", "http://www.brighthand.com", "Brighthand.com is a website dedicated to bringing the latest news, reviews and pricing for all types of handheld devices", 16);
    INSERT INTO news VALUES (0, "Engadget Mobile", "http://www.engadgetmobile.com/rss.xml", "http://www.engadgetmobile.com", "Engadget Mobile", 16);
    INSERT INTO news VALUES (0, "Geekzone", "http://www.geekzone.co.nz/geekzone_rss.asp", "http://www.geekzone.co.nz", "IT, mobility, wireless and handheld news", 16);
    INSERT INTO news VALUES (0, "iPodlounge", "http://www.ipodlounge.com/index.xml", "http://www.iLounge.com", "iLounge iPod Accessory Reviews", 16);
    INSERT INTO news VALUES (0, "Mobile Opportunity", "http://mobileopportunity.blogspot.com/feeds/posts/default", "http://mobileopportunity.blogspot.com/", "Comments on the tech industry, with a focus on mobile, wireless, &amp; the web", 16);
    INSERT INTO news VALUES (0, "MobileBurn.com", "http://www.mobileburn.com/xml/rss2.jsp", "http://www.MobileBurn.com/", "Reviews and News from the Mobile Phone and Bluetooth Industries", 16);
    INSERT INTO news VALUES (0, "MobileCrunch", "http://feeds.feedburner.com/Mobilecrunch", "http://www.mobilecrunch.com", "All About Mobile 2.0", 16);
    INSERT INTO news VALUES (0, "mocoNews", "http://feeds.moconews.net/moconews/", "http://moconews.net/", "Unhealthily Obsessed With Mobile Content", 16);
    INSERT INTO news VALUES (0, "Phone Scoop - Latest News", "http://www.phonescoop.com/rss/news.php", "http://www.phonescoop.com/", "Phone Scoop is a comprehensive resource for mobile phone enthusiasts, professionals, shoppers, and users.", 16);
    INSERT INTO news VALUES (0, "Pocket PC Addict", "http://www.pocketpcaddict.com/backend.php", "http://www.pocketpcaddict.com", "The Latest News from PocketPCAddict.com - Your source for the latest Windows Mobile news.", 16);
    INSERT INTO news VALUES (0, "Pocket PC Thoughts", "http://www.pocketpcthoughts.com/xml", "http://www.PocketPCThoughts.com", "Pocket PC Thoughts - Daily News, Views, Rants and Raves", 16);
    INSERT INTO news VALUES (0, "pocketnow.com", "http://www.pocketnow.com/xml/index.xml", "http://pocketnow.com/", "pocketnow.com", 16);
    INSERT INTO news VALUES (0, "Softpedia - Handheld Devices", "http://handheld.softpedia.com/backend.xml", "http://handheld.softpedia.com/", "Softpedia - Handheld Devices", 16);
    INSERT INTO news VALUES (0, "Softpedia - Mobile", "http://mobile.softpedia.com/backend.xml", "http://mobile.softpedia.com/", "Softpedia - Mobile", 16);
    INSERT INTO news VALUES (0, "Softpedia - Telecoms", "http://news.softpedia.com/newsRSS/Telecoms-10.xml", "http://news.softpedia.com", "Softpedia News - Telecoms", 16);
  --- INSERT INTO subcategory VALUES ("Programming Languages"); ---
    INSERT INTO news VALUES (0, "About.com Focus on JavaScript", "http://z.about.com/6/g/javascript/b/rss2.xml", "http://javascript.about.com/", "Get the latest headlines from the About.com Focus on JavaScript GuideSite.", 17);
    INSERT INTO news VALUES (0, "A List Apart", "http://www.alistapart.com/site/rss", "http://www.alistapart.com/articles/", 17);
    INSERT INTO news VALUES (0, "CodeBetter.Com", "http://codebetter.com/blogs/MainFeed.aspx", "http://codebetter.com/blogs/", "Stuff you need to code better!", 17);
    INSERT INTO news VALUES (0, "Coding Horror", "http://www.codinghorror.com/blog/index.xml", "http://www.codinghorror.com/blog/", "programming and human factors - Jeff Atwood", 17);
    INSERT INTO news VALUES (0, "dzone.com", "http://www.dzone.com/feed/frontpage/rss.xml", "http://www.dzone.com/links/", "dzone.com: fresh links for developers", 17);
    INSERT INTO news VALUES (0, "Java", "http://developers.sun.com/rss/sdn.xml", "http://developers.sun.com/index.jsp", "Get this week's featured content on the Sun Developer Network (SDN) home page.", 17);
    INSERT INTO news VALUES (0, "JavaScript Tips", "http://www.webreference.com/js/tips/channels/last1515.rdf", "http://www.webreference.com/js/tips/", "Doc JavaScript's Tip of the Day Archive", 17);
    INSERT INTO news VALUES (0, "jQuery Blog", "http://jquery.com/blog/feed/", "http://blog.jquery.com", "New Wave Javascript.", 17);
    INSERT INTO news VALUES (0, "PHP News", "http://www.php.net/news.rss", "http://www.php.net/", "The PHP scripting language web site", 17);
    INSERT INTO news VALUES (0, "PHPBuilder.com", "http://phpbuilder.com/rss_feed.php?type=articles&amp;limit=20", "http://phpbuilder.com", "Newest Articles and How-To's on PHPBuilder.com", 17);
    INSERT INTO news VALUES (0, "Planet JDK", "http://planetjdk.org/feed.atom-1.0", "http://planetjdk.org", "News and views from the Java SE Development-Kit Community", 17);
    INSERT INTO news VALUES (0, "programming", "http://programming.reddit.com/.rss", "http://www.reddit.com/r/programming/", 17);
    INSERT INTO news VALUES (0, "Python News", "http://www.python.org/channews.rdf", "http://www.python.org/", "Python-related news and announcements.&#xA; Python is an interpreted, interactive, object-oriented&#xA; programming language.", 17);
    INSERT INTO news VALUES (0, "QuirksBlog", "http://www.quirksmode.org/blog/atom.xml", "http://www.quirksmode.org/blog/", 17);
    INSERT INTO news VALUES (0, "Servlets.com", "http://www.servlets.com/blog/index.rss", "http://www.servlets.com/blog/", "Java, Open Source, XML, Web Services, and (gasp) .NET", 17);
  --- INSERT INTO subcategory VALUES ("Security"); ---
    INSERT INTO news VALUES (0, "Computer World Security Knowledge Center", "http://www.computerworld.com/news/xml/0,5000,73,00.xml", "http://www.computerworld.com/", 18);
    INSERT INTO news VALUES (0, "Darknet - The Darkside", "http://feeds.feedburner.com/darknethackers", "http://www.darknet.org.uk", "Ethical Hacking, Penetration Testing &amp; Computer Security", 18);
    INSERT INTO news VALUES (0, "F-Secure Antivirus Research Weblog", "http://www.f-secure.com/weblog/weblog.rdf", "http://www.f-secure.com/weblog", "Weblog of F-Secure Antivirus Research Team", 18);
    INSERT INTO news VALUES (0, "ha.ckers.org web application security lab", "http://ha.ckers.org/blog/feed/", "http://ha.ckers.org/blog", "Web Application Security Blog", 18);
    INSERT INTO news VALUES (0, "Schneier on Security", "http://www.schneier.com/blog/index.rdf", "", "A blog covering security and security technology.", 18);
    INSERT INTO news VALUES (0, "SecurityFocus News", "http://www.securityfocus.com/rss/news.xml", "http://www.securityfocus.com", "&#xA;SecurityFocus is the most comprehensive and trusted source of security&#xA;information on the Internet. We are a vendor-neutral site that provides&#xA;objective, timely and comprehensive security information to all members of&#xA;the security community, from end users, security hobbyists and network&#xA;administrators to security consultants, IT Managers, CIOs and CSOs.&#xA;", 18);
    INSERT INTO news VALUES (0, "TaoSecurity", "http://taosecurity.blogspot.com/atom.xml", "http://taosecurity.blogspot.com/", "Richard Bejtlich's blog on digital security and the practices of network security monitoring, incident response, and forensics.", 18);
  --- INSERT INTO subcategory VALUES ("Software"); ---
    INSERT INTO news VALUES (0, "BetaNews.Com", "http://www.betanews.com/mnn.php3", "http://www.betanews.com", "Technology News and IT Business Intelligence", 19);
    INSERT INTO news VALUES (0, "CNET Download", "http://www.download.com/3409-2001-0-10.xml?tag=lr_rss", "http://download.cnet.com/", "Top Rated in Windows from Download.com", 19);
    INSERT INTO news VALUES (0, "FileHippo.com", "http://feeds.feedburner.com/filehippo", "http://www.filehippo.com/", "FileHippo.com provides you with all the latest software news and updates to download from one site!", 19);
    INSERT INTO news VALUES (0, "FilePlaza", "http://www.fileplaza.com/lastaddedsoftware.rss", "http://www.fileplaza.com/default.rss", "FilePlaza is the best source of free software downloads. Free download - games, screen savers, free wallpaper, java download, anti virus software and desktop software for windows, unix and palm.", 19);
    INSERT INTO news VALUES (0, "Free Vista Files", "http://www.freevistafiles.com/rss/new_software.xml", "http://www.freevistafiles.com/", "FreeVistaFiles.com - Free Windows Vista Files", 19);
    INSERT INTO news VALUES (0, "Giveaway of the Day", "http://www.giveawayoftheday.com/feed/", "http://www.giveawayoftheday.com", "free licensed software daily", 19);
    INSERT INTO news VALUES (0, "MajorGeeks.com", "http://www.majorgeeks.com/backend.php?id=120", "http://www.majorgeeks.com/index.php", "Last 20 items on MajorGeeks.com", 19);
    INSERT INTO news VALUES (0, "Mashable!", "http://feeds2.feedburner.com/Mashable", "http://mashable.com", "Social software and social networking 2.0.", 19);  
    INSERT INTO news VALUES (0, "PortableApps.com", "http://portableapps.com/feeds/general", "http://portableapps.com", 19);
    INSERT INTO news VALUES (0, "Slashdot", "http://slashdot.org/slashdot.rss", "", "News for nerds, stuff that matters", 19);
    INSERT INTO news VALUES (0, "SnapFiles latest software", "http://www.webattack.com/webattack.xml", "http://www.snapfiles.com/", "The latest shareware and freeware downloads on the web", 19);
    INSERT INTO news VALUES (0, "Softpedia - Drivers", "http://drivers.softpedia.com/backend.xml", "http://drivers.softpedia.com/", "Softpedia - Drivers - All", 19);
    INSERT INTO news VALUES (0, "Softpedia - Linux", "http://linux.softpedia.com/backend.xml", "http://linux.softpedia.com/", "Softpedia - Linux - All", 19);
    INSERT INTO news VALUES (0, "Softpedia - Mac OS", "http://mac.softpedia.com/backend.xml", "http://mac.softpedia.com/", "Softpedia - Mac OS - All", 19);
    INSERT INTO news VALUES (0, "Softpedia - Webscripts", "http://webscripts.softpedia.com/backend.xml", "http://webscripts.softpedia.com/", "Softpedia - Webscripts - All", 19);
    INSERT INTO news VALUES (0, "Softpedia - Windows", "http://www.softpedia.com/backend.xml", "http://win.softpedia.com/", "Softpedia - Windows - All", 19);
  -- GENERAL Technology
    INSERT INTO news VALUES (0, "Anand Tech", "http://www.anandtech.com/rss/articlefeed.aspx", "http://www.anandtech.com", "This channel features the latest computer hardware related articles.", 20);
    INSERT INTO news VALUES (0, "Ars Technica", "http://arstechnica.com/index.ars/rss", "http://arstechnica.com/", 20);
    INSERT INTO news VALUES (0, "BBC News | Technology | World Edition", "http://news.bbc.co.uk/rss/newsonline_world_edition/technology/rss091.xml", "http://news.bbc.co.uk/go/rss/-/2/hi/technology/default.stm", "Get the latest BBC Technology News: breaking news and analysis on computing, the web, blogs, games, gadgets, social media, broadband and more.", 20);
    INSERT INTO news VALUES (0, "CNET News", "http://rss.com.com/2547-12-0-20.xml", "http://news.cnet.com/", "Tech news and business reports by CNET News. Focused on&#xA;information technology, core topics include computers, hardware, software,&#xA;networking, and Internet media.", 20);
    INSERT INTO news VALUES (0, "Computer World", "http://www.computerworld.com/news/xml/10/0,5009,,00.xml", "http://www.computerworld.com/", 20);
    INSERT INTO news VALUES (0, "digg - tech news", "http://digg.com/rss/index.xml", "http://digg.com/", "digg.com: Stories / Popular", 20);
    INSERT INTO news VALUES (0, "Digital Photography Review", "http://www.dpreview.com/news/dpr.rdf", "http://www.dpreview.com/", "Digital Photography Review, Latest digital camera news, camera reviews, galleries, technology and comparisons.", 20);
    INSERT INTO news VALUES (0, "eWEEK Technology News", "http://rssnewsapps.ziffdavis.com/tech.xml", "http://www.eweek.com", "eWeek - RSS Feeds", 20);
    INSERT INTO news VALUES (0, "IRC-Junkie.org - IRC News", "http://www.irc-junkie.org/feed/", "http://www.irc-junkie.org", 20);
    INSERT INTO news VALUES (0, "Joel on Software", "http://www.joelonsoftware.com/rss.xml", "http://www.joelonsoftware.com", "Painless Software Management", 20);
    INSERT INTO news VALUES (0, "MAKE: Blog", "http://www.makezine.com/blog/index.rdf", "http://blog.makezine.com/", "MAKE is a quarterly publication from O'Reilly for those who just can't stop tinkering, disassembling, re-creating, and inventing cool new uses for the technology in our lives.  It's the first do-it-yourself magazine dedicated to the incorrigible and chronically incurable technology enthusiast in all of us.  MAKE celebrates your right to tweak, hack, and bend technology any way you want.", 20);
    INSERT INTO news VALUES (0, "Planet Eclipse", "http://www.planeteclipse.org/planet/atom.xml", "http://planeteclipse.org/planet/", 20);
    INSERT INTO news VALUES (0, "Shacknews", "http://www.shacknews.com/headlines.rdf", "http://www.shacknews.com/", "Shacknews - Your source for the latest video game news", 20);
    INSERT INTO news VALUES (0, "Softpedia Gadgets News", "http://gadgets.softpedia.com/newsRSS/Global-0.xml", "http://gadgets.softpedia.com/news/", "Softpedia Gadgets News - Global", 20);
    INSERT INTO news VALUES (0, "Softpedia News", "http://news.softpedia.com/newsRSS/Global-0.xml", "http://news.softpedia.com", "Softpedia News - Global", 20);
    INSERT INTO news VALUES (0, "Wired News", "http://www.wired.com/news_drop/netcenter/netcenter.rdf", "http://www.wired.com/rss/index.xml", "Top Stories&lt;img src=&quot;http://wired-vig.wired.com/rss_views/index.gif&quot;&gt;", 20);
    INSERT INTO news VALUES (0, "ZDNet News - Front Door", "http://news.zdnet.com/2509-1_22-0-20.xml", "http://updates.zdnet.com/index.php?t=7", "News items", 20);
  --- INSERT INTO subcategory VALUES (2,"Windows"); ---
    INSERT INTO news VALUES (0, "Ed Bott's Microsoft Report", "http://blogs.zdnet.com/Bott/wp-rss2.php", "http://blogs.zdnet.com/Bott", "Windows and a whole lot more", 21);
    INSERT INTO news VALUES (0, "SearchWindowsServer", "http://rss.techtarget.com/43.xml", "http://searchwindowsserver.techtarget.com?track=sy43", "Expert advice and reference materials about Windows-based systems and hardware for IT administrators.", 21);
    INSERT INTO news VALUES (0, "Softpedia - Microsoft", "http://news.softpedia.com/newsRSS/Microsoft-6.xml", "http://news.softpedia.com", "Softpedia News - Microsoft", 21);
    INSERT INTO news VALUES (0, "Windows Connected", "http://feeds.feedburner.com/WindowsConnected", "http://windowsconnected.com/blogs/", "Are You Connected?", 21);
    INSERT INTO news VALUES (0, "Windows Experience Blog", "http://windowsvistablog.com/blogs/windowsexperience/rss.aspx", "http://windowsteamblog.com/blogs/windowsexperience/default.aspx", 21);
    INSERT INTO news VALUES (0, "Windows Server Division WebLog", "http://blogs.technet.com/windowsserver/rss.xml", "http://blogs.technet.com/windowsserver/default.aspx", "Focusing on Windows Server 2008 and Hyper-V, Storage Server, High Performance Computing, Essential Business Server ,and Small Business Server", 21);
    INSERT INTO news VALUES (0, "Windows Vista Weblog", "http://www.windowsvistaweblog.com/feed/", "http://www.everyjoe.com/windowsvistaweblog", "Windows Vista and IE7 News - Tips for Using Windows Vista", 21);
    INSERT INTO news VALUES (0, "WindowsNetworking.com", "http://rss.windowsnetworking.com/allnews.xml", "http://www.WindowsNetworking.com/", "WindowsNetworking.com features a wealth of tutorials on various Windows networking related topics such as setting up Windows NT/XP/2000/2003 networks, troubleshooting, connectivity and more. Also includes a comprehensive archive of reviewed networking software.", 21);
  --- INSERT INTO subcategory VALUES ("Wireless"); ---
    INSERT INTO news VALUES (0, "Computer World Mobile", "http://www.computerworld.com/news/xml/0,5000,68,00.xml", "http://www.computerworld.com/", 22);
    INSERT INTO news VALUES (0, "FierceWireless", "http://www.fiercewireless.com/rss.xml", "http://www.fiercewireless.com/news", "Latest News Posts", 22);
    INSERT INTO news VALUES (0, "InfoWorld: Wireless", "http://www.infoworld.com/rss/wireless.rdf", "http://www.infoworld.com/t/2085", 22);
    INSERT INTO news VALUES (0, "MobileTechNews", "http://www.mobiletechnews.com/headlines.rdf", "http://www.mobiletechnews.com", "Mobile Technology News. If you are an investor, &#xA; technologist, or decision maker in the mobile/wireless industry, this site&#xA; is for you. Stay up to date with the daily developments in this fast moving and fast growing industry sector.", 22);
    INSERT INTO news VALUES (0, "MuniWireless", "http://feeds.feedburner.com/muniwireless/", "http://www.muniwireless.com", "Municipal wireless, citywide WiFi, WiMAX, broadband news", 22);
    INSERT INTO news VALUES (0, "Network World on Wireless and Mobile", "http://www.networkworld.com/rss/wireless.xml", "http://www.networkworld.com/topics/wireless.html", "Breaking wireless and mobile news and analysis from NetworkWorld.com.", 22);
    INSERT INTO news VALUES (0, "Wi-Fi Networking News", "http://wifinetnews.com/rss2.xml", "http://wifinetnews.com/", "Daily reporting about Wi-Fi and other wireless data, including hotspots, home networks, commuter Wi-Fi, and in-flight Internet.", 22);
    INSERT INTO news VALUES (0, "WiFi Planet", "http://www.wi-fiplanet.com/icom_includes/feeds/80211/xml_front-news-10.xml", "http://www.wi-fiplanet.com", "The Source for Wi-Fi Business and Technology", 22);
    INSERT INTO news VALUES (0, "Yahoo! News: Wireless and Mobile Technology", "http://rss.news.yahoo.com/rss/wireless", "http://news.yahoo.com/i/1899", "Wireless and Mobile Technology", 22);
-- INSERT INTO category VALUES ("Entertainment"); --
  --- INSERT INTO subcategory VALUES ("Art"); ---
    INSERT INTO news VALUES (0, "Art News Blog", "http://www.artnewsblog.com/atom.xml", "http://www.artnewsblog.com/index.htm", "Art News Blog is a selection of visual art news, art reviews and art related stories online. We search the web for some of the more interesting art news stories published each day.", 23);    
    INSERT INTO news VALUES (0, "Drawn!", "http://drawn.ca/feed/", "http://drawn.ca", "llustration, Comics, Animation, and Cartoon Art", 23);
    INSERT INTO news VALUES (0, "lines and colors", "http://www.linesandcolors.com/feed/", "http://www.linesandcolors.com", 23);
    INSERT INTO news VALUES (0, "Modern Art Notes", "http://www.artsjournal.com/man/rss.xml", "http://www.artsjournal.com/man/", "Tyler Green's modern &amp; contemporary art blog", 23);
    INSERT INTO news VALUES (0, "Rhizome Inclusive", "http://rhizome.org/syndicate/fp.rss", "http://rhizome.org", 23);
    INSERT INTO news VALUES (0, "VVORK", "http://www.vvork.com/?feed=rss2", "http://www.vvork.com", "art, original content and curation", 23);
    INSERT INTO news VALUES (0, "we make money not art", "http://feeds.we-make-money-not-art.com/wmmna", "http://www.we-make-money-not-art.com/", 23);
    INSERT INTO news VALUES (0, "Wooster Collective", "http://www.woostercollective.com/atom.xml", "http://www.woostercollective.com/", 23);
  --- INSERT INTO subcategory VALUES ("Books"); ---
    INSERT INTO news VALUES (0, "Books News and Reviews", "http://www.guardian.co.uk/rssfeed/0,,10,00.xml", "http://www.guardian.co.uk/books", "Latest news and features from guardian.co.uk, the world's leading liberal voice", 24);
    INSERT INTO news VALUES (0, "London Review of Books", "http://www.lrb.co.uk/homerss.xml", "http://www.lrb.co.uk/", "Literary review publishing essay-length book reviews and topical articles on politics, literature, history, philosophy, science and the arts by leading writers and thinkers", 24);
    INSERT INTO news VALUES (0, "NPR Topics: Books", "http://www.npr.org/rss/rss.php?id=1032", "http://www.npr.org/templates/story/story.php?storyId=1032&amp;ft=1&amp;f=1032", "Book reviews, interviews with authors, and NPR Book Tour, a weekly audio feature and podcast where leading authors read and discuss their work. Subscribe to the RSS feed.", 24);
    INSERT INTO news VALUES (0, "Paper Cuts", "http://papercuts.blogs.nytimes.com/rss2.xml", "http://papercuts.blogs.nytimes.com/", "A Blog About Books", 24);
    INSERT INTO news VALUES (0, "Salon: Books", "http://feeds.salon.com/salon/books", "http://dir.salon.com/topics/books/?source=rss&amp;aim=/topics/books/", "Book reviews, author interviews and publishing news from Salon critics and staff.", 24);
    INSERT INTO news VALUES (0, "The Literary Saloon", "http://www.complete-review.com/saloon/rss.xml", "http://www.complete-review.com/saloon/index.htm", "opinionated commentary on literary matters", 24);
    INSERT INTO news VALUES (0, "The New York Review of Books", "http://feeds.feedburner.com/nybooks", "http://pipes.yahoo.com/pipes/pipe.info?_id=irFhAzfu3RG_BLic_w6H4A", "Main RSS feed for nybooks.com, includes articles and podcasts.", 24);
  --- INSERT INTO subcategory VALUES ("Cars"); ---
    INSERT INTO news VALUES (0, "Autoblog", "http://www.autoblog.com/rss.xml", "http://www.autoblog.com", "Autoblog", 25);
    INSERT INTO news VALUES (0, "autoevolution", "http://www.autoevolution.com/rss/backend.xml", "http://www.autoevolution.com/", "autoevolution", 25);
    INSERT INTO news VALUES (0, "Car and Driver Blog", "http://www.caranddriver.com/rssfeed.asp?rss_feed_id=38", "http://blog.caranddriver.com", "Automotive News Blog at CARandDRIVER.com - Car News Resource", 25);
    INSERT INTO news VALUES (0, "Jalopnik", "http://feeds.gawker.com/jalopnik/full", "http://jalopnik.com", "Jalopnik loves cars. Secret cars, concept cars, flying cars, vintage cars, tricked-out cars, red cars, black cars, blonde cars -- sometimes, cars just because of the curve of a hood.", 25);
    INSERT INTO news VALUES (0, "Leftlane", "http://www.leftlanenews.com/wp-rss2.php", "http://www.leftlanenews.com", "Car news, reviews, and specs for the auto-industry", 25);
    INSERT INTO news VALUES (0, "PopularMechanics.com", "http://feeds.popularmechanics.com/pm/blogs/automotive_news", "http://www.origin.popularmechanics.com/blogs/automotive_news/", "Informative articles on automotive technology, car parts, do it yourself repair, and used cars.", 25);
  --- INSERT INTO subcategory VALUES ("Comics"); ---
    INSERT INTO news VALUES (0, "Dilbert Daily Strip", "http://feeds.dilbert.com/DilbertDailyStrip", "http://dilbert.com/", "The Official Dilbert Daily Comic Strip RSS Feed", 26);
    INSERT INTO news VALUES (0, "Dinosaur Comics", "http://www.rsspect.com/rss/qwantz.xml", "http://www.qwantz.com", "Sexy exciting comics for all the awesome people of the world!", 26);
    INSERT INTO news VALUES (0, "PHD Comics", "http://www.phdcomics.com/gradfeed.php", "http://www.phdcomics.com", "Providing global up-to-the-minute procrastination!", 26);
    INSERT INTO news VALUES (0, "PvPonline", "http://www.pvponline.com/rss/?section=article", "http://www.pvponline.com", "The Daily Online Comic", 26);
    INSERT INTO news VALUES (0, "xkcd.com", "http://xkcd.com/rss.xml", "http://xkcd.com/", "xkcd.com: A webcomic of romance and math humor.", 26);
    --- INSERT INTO subcategory VALUES ("Dance"); ---
    INSERT INTO news VALUES (0, "DanceMusicBlog", "http://feeds.feedburner.com/DmbDanceMusicBlogPodcast", "http://www.dancemusicblog.com/", "Dance Music News, DJ Mix Podcast, Reviews, Free MP3s, and Events All in One Rockin' Blog", 27);
    --- INSERT INTO subcategory VALUES ("Games"); ---
    INSERT INTO news VALUES (0, "GamersCircle", "http://www.gamerscircle.net/b2rss2.php", "http://www.gamerscircle.net", "One stop shop for Gaming, Technology and Industry News.", 28);
    INSERT INTO news VALUES (0, "IGN Complete", "http://scripts.ign.com/rss/ign.all.2.0.xml", "http://www.ign.com", "IGN is the ultimate gaming and entertainment resource featuring award winning coverage of video games, cheats, movies, music, cars, sports, babes, comics and gear.", 28);
    INSERT INTO news VALUES (0, "Joystiq", "http://www.joystiq.com/rss.xml", "http://www.joystiq.com", "Joystiq", 28);
    INSERT INTO news VALUES (0, "Joystiq [Nintendo]", "http://www.nintendowiifanboy.com/rss.xml", "http://nintendo.joystiq.com", "Joystiq [Nintendo]", 28);
    INSERT INTO news VALUES (0, "Joystiq [Xbox]", "http://www.xbox360fanboy.com/rss.xml", "http://xbox.joystiq.com", "Joystiq [Xbox]", 28);
    INSERT INTO news VALUES (0, "Kotaku", "http://www.kotaku.com/index.xml", "http://kotaku.com", "As if you don't waste enough of your time in a gamer's haze, here's Kotaku: a gamer's guide that goes beyond the press release. Gossip, cheats, criticism, design, nostalgia, prediction. Don't get a life just yet.", 28);
    INSERT INTO news VALUES (0, "Slashdot: Games", "http://slashdot.org/games.rss", "", "News for nerds, stuff that matters", 28);
    INSERT INTO news VALUES (0, "Softpedia - Games", "http://games.softpedia.com/backend.xml", "http://games.softpedia.com/", "Softpedia - Games - All", 28);
    INSERT INTO news VALUES (0, "Xbox Live's Major Nelson", "http://feeds.feedburner.com/MajorNelson", "http://majornelson.com/default.aspx", "Xbox Lives Director of Programmings personal blog about Xbox and more.", 28);
  --- INSERT INTO subcategory VALUES ("Humor"); ---
    INSERT INTO news VALUES (0, "BBspot", "http://www.bbspot.com/rdf/bbspot.rdf", "http://www.bbspot.com", "Satire for Smart People; The Truth for You", 29);
    INSERT INTO news VALUES (0, "Best of Craigslist", "http://www.craigslist.org/about/best/all/index.rss", "http://www.craigslist.org/about/best/", "Best postings from craigslist.org, selected by readers", 29);
    INSERT INTO news VALUES (0, "CollegeHumor", "http://feeds.collegehumor.com/collegehumor/articles", "http://www.collegehumor.com", 29);
    INSERT INTO news VALUES (0, "Darkgate Comic Slurper", "http://darkgate.net/comic/feed.php?foxtrot&amp;dilbert&amp;doonesbury&amp;sherman&amp;userf&amp;stonesoup", "http://darkgate.net/comic/", "The latest strips of your favourite web-comics slurped from the internet.  Configure your preferences at &lt;a href=&quot;http://darkgate.net/comic/&quot;&gt;http://darkgate.net/comic/&lt;/a&gt;.", 29);
    INSERT INTO news VALUES (0, "Overheard in the Office", "http://www.overheardintheoffice.com/index.xml", "http://www.overheardintheoffice.com/", "The Voice of the Cubicle", 29);
  --- INSERT INTO subcategory VALUES ("Movies"); ----
  INSERT INTO news VALUES (0, "Cinematical", "http://www.cinematical.com/rss.xml", "http://www.cinematical.com", "Cinematical", 30);
  INSERT INTO news VALUES (0, "E! Online (US) - Top Stories", "http://www.eonline.com/syndication/feeds/rssfeeds/topstories.xml", "http://www.eonline.com/", "News from across the show-biz spectrum-TV, movies, music and celebrities", 30);
  INSERT INTO news VALUES (0, "Gawker", "http://www.gawker.com/index.xml", "http://gawker.com", "Gawker is the Manhattan media gossip sheet.", 30);
  INSERT INTO news VALUES (0, "Latest Movie Trailers", "http://images.apple.com/trailers/rss/newtrailers.rss", "http://www.apple.com/trailers/", "Recently added Movie Trailers.", 30);
  INSERT INTO news VALUES (0, "The Movie Blog", "http://feeds.feedburner.com/themovieblog/VkTh", "http://themovieblog.com", "The official home of correct movie opinions", 30);  
  INSERT INTO news VALUES (0, "Yahoo! Entertainment News", "http://rss.news.yahoo.com/rss/entertainment", "http://news.yahoo.com/i/762", "Entertainment News", 30);
  --- INSERT INTO subcategory VALUES ("Music"); ---
  INSERT INTO news VALUES (0, "CMT News", "http://www.cmt.com/rss/news/latest.jhtml", "http://www.cmt.com/news/", "All the news from country music's most reliable source.", 31);
  INSERT INTO news VALUES (0, "gorillavsbear.net", "http://gorillavsbear.blogspot.com/atom.xml", "http://gorillavsbear.blogspot.com/", "mp3s for sampling only.  you should really be buying this stuff on vinyl anyway. if you are the owner of a sound file, and would like it removed, &lt;a href=&quot;mailto:chrismc99@gmail.com&quot;&gt;holler at us&lt;/a&gt;.", 31);
  INSERT INTO news VALUES (0, "Idolator", "http://www.idolator.com/index.xml", "http://idolator.com", "Music News, Reviews, And Gossip", 31);
  INSERT INTO news VALUES (0, "iTunes 25 Just Added Albums", "http://phobos.apple.com/WebObjects/MZStore.woa/wpa/MRSS/justadded/limit=25/rss.xml", "http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewNewReleases?pageType=justAdded&amp;id=1", "iTunes Store: 25 Just Added Albums", 31);
  INSERT INTO news VALUES (0, "iTunes Top 25 Songs", "http://phobos.apple.com/WebObjects/MZStore.woa/wpa/MRSS/topsongs/limit=25/rss.xml", "http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewTop?id=1&amp;popId=1", "iTunes Store: Today's Top 25 Songs", 31);
  INSERT INTO news VALUES (0, "MTV News", "http://www.mtv.com/rss/news/latest.jhtml", "http://www.mtv.com/news/", "Up to the minute news on music and pop culture.", 31);
  INSERT INTO news VALUES (0, "NME News", "http://www.nme.com/rss/news.xml", "http://nme.com/news?alt=rss", "NME News", 31);
  INSERT INTO news VALUES (0, "RollingStone.com Music News", "http://www.rollingstone.com/rssxml/music_news.xml", "http://www.rollingstone.com/news?source=music_news_rssfeed", "Rolling Stone Magazine comes to life online with music news, videos&#xA;and photo galleries, the latest movie and music reviews, cover stories and online exclusives.", 31);
  INSERT INTO news VALUES (0, "Stereogum", "http://feeds.feedburner.com/stereogum/cBYa", "http://stereogum.com/", "The #1 music blog.", 31);
  INSERT INTO news VALUES (0, "VH1 News", "http://www.vh1.com/rss/news/latest.jhtml", "http://www.vh1.com/news/", "Full pop music coverage and daily entertainment updates.", 31);
  --- INSERT INTO subcategory VALUES ("Quotes"); ---
    INSERT INTO news VALUES (0, "Famous Quotes", "http://quotes.wordpress.com/feed/", "http://quotes.wordpress.com", "Famous Quotes and Famous Sayings from Famous People", 32);
    INSERT INTO news VALUES (0, "Motivational Quote", "http://www.quotationspage.com/data/mqotd.rss", "http://www.quotationspage.com/mqotd.html", "Four motivational quotations each day from The Quotations Page", 32);
    INSERT INTO news VALUES (0, "Quotes of the Day", "http://feeds.feedburner.com/qotd", "", "Daily Quotations", 32);
    INSERT INTO news VALUES (0, "Wisdom Quotes", "http://www.wisdomquotes.com/index.xml", "http://www.wisdomquotes.com/", "Quotations to inspire and challenge", 32);
  --- INSERT INTO subcategory VALUES ("Science Fiction"); ----
    INSERT INTO news VALUES (0, "GateWorld News", "http://www.gateworld.net/news/rss_gateworld.xml", "http://www.gateworld.net/news", "The Latest News From the Stargate Universe!", 33);
    INSERT INTO news VALUES (0, "Locus Online News", "http://www.mobileread.com/feeds/locus.xml", "http://www.locusmag.com/News/", 33);
    INSERT INTO news VALUES (0, "SF Signal", "http://www.sfsignal.com/index.rdf", "", "A science fiction blog featuring science fiction book reviews and with frequent ramblings on fantasy, computers and the web.", 33);
    INSERT INTO news VALUES (0, "Slice of SciFi", "http://feeds.feedburner.com/sliceofscifinews", "http://www.sliceofscifi.com", "Science Fiction TV &amp; Movie News, Interviews &amp; more: News about science fiction TV, movies and more from Michael, Summer, Brian, Sam and his news crew. If it\'s on the tube or silver screen, and someone is toting a blaster or a longsword, we\'re probably talking about it!", 33);
    INSERT INTO news VALUES (0, "TrekMovie.com", "http://trekmovie.com/feed/", "http://trekmovie.com", "the source for Star Trek news and information", 33);
    INSERT INTO news VALUES (0, "TrekToday", "http://www.trektoday.com/headlines/rss.xml", "http://www.trektoday.com/content", "Daily Star Trek news", 33);
  --- INSERT INTO subcategory VALUES ("TV"); ----    
    INSERT INTO news VALUES (0, "People.com Latest News", "http://rss.people.com/web/people/rss/topheadlines/index.xml", "http://www.people.com/news", "Latest News from People.com", 34);
    INSERT INTO news VALUES (0, "TV Guide", "http://rss.tvguide.com/breakingnews", "http://www.tvguide.com", "TV Guide", 34);
    INSERT INTO news VALUES (0, "TV Squad", "http://www.tvsquad.com/rss.xml", "http://www.tvsquad.com", "TV Squad", 34);
    INSERT INTO news VALUES (0, "Variety.com - Front Page", "http://www.variety.com/rss.asp?categoryid=10", "http://www.variety.com", "The premier source of entertainment news. Turn to Variety.com for timely, credible articles, reviews and analysis of film, TV, music, theater, video, gaming and movie and television production -- information vital to your showbiz career.", 34);
-- INSERT INTO category VALUES ("Humanities"); --    
  --- INSERT INTO subcategory VALUES ("Genealogy"); ---
    INSERT INTO news VALUES (0, "GeneaMusings", "http://www.geneamusings.com/feeds/posts/default?alt=rss", "http://www.geneamusings.com/", "Genealogy research tips and techniques, genealogy news items and commentary, genealogy humor, San Diego genealogy society news, family history research and some family history stories.", 35);
  --- INSERT INTO subcategory VALUES ("History"); ---
    INSERT INTO news VALUES (0, "The History Blog", "http://www.thehistoryblog.com/feed", "http://www.thehistoryblog.com/", "Primarily European ancient and medieval history blog, but also undiscriminating when it comes to history, so covering just about anything that catches the author's eye", 36);
  --- INSERT INTO subcategory VALUES ("Language and Linguistics"); ---
    INSERT INTO news VALUES (0, "BibliOdyssey", "http://bibliodyssey.blogspot.com/feeds/posts/default", "http://bibliodyssey.blogspot.com/", "Bibliodyssey - Eclectic Bookart", 37);
  --- INSERT INTO subcategory VALUES ("Philosophy"); ---
    INSERT INTO news VALUES (0, "Leiter Reports: A Philosophy Blog", "http://leiterreports.typepad.com/blog/rss.xml", "http://leiterreports.typepad.com/blog/", "News and views about philosophy, the academic profession, academic freedom, intellectual culture...and a bit of poetry.", 38);
  --- INSERT INTO subcategory VALUES ("Religion"); ---
    INSERT INTO news VALUES (0, "Religion Clause", "http://religionclause.blogspot.com/feeds/posts/default?alt=rss", "http://religionclause.blogspot.com/", "Congress shall make no law respecting an establishment of religion, or prohibiting the free exercise thereof... --US Const., Amend. 1", 39);
    INSERT INTO news VALUES (0, "ReligionNewsBlog", "http://feeds.feedburner.com/ReligionNewsBlog", "http://www.religionnewsblog.com/", "Religion news articles about religious cults, sects, world religions, and related issues.", 39);
    INSERT INTO news VALUES (0, "Hindu Devotional Blog", "http://feeds.feedburner.com/HinduDevotionalBlog", "http://www.hindudevotionalblog.com/", "'Atithi Devo Bhava' a Sanskrit phrase which means 'Guest is God'....We Welcome All Distinguished Guests to this spiritual &amp; religious.", 39);
  --- INSERT INTO subcategory VALUES ("Spirituality"); ---
    INSERT INTO news VALUES (0, "Spirituality & Practice: Spiritual Literacy Blog", "http://www.spiritualityandpractice.com/rss.php", "http://www.spiritualityandpractice.com/blogs/blog.php", "Life is a sacred adventure. Every day we encounter signs that point to the active presence of Spirit in the world around us. Spiritual literacy is the ability to read the signs written in the texts of our own experiences.", 40);
-- INSERT INTO category VALUES ("Health"); --
  --- INSERT INTO subcategory VALUES ("Diet and Nutrition"); ---
  INSERT INTO news VALUES (0, "Bay Area Bites", "http://www.kqed.org/weblog/food/atom.xml", "http://blogs.kqed.org/bayareabites", "Culinary Rants &amp; Raves from Bay Area Foodies and Professionals", 41);
  INSERT INTO news VALUES (0, "Becks &amp; Posh", "http://becksposhnosh.blogspot.com/feeds/posts/default", "http://becksposhnosh.blogspot.com/", 41);
  INSERT INTO news VALUES (0, "Chocolate &amp; Zucchini", "http://chocolateandzucchini.com/index.rdf", "http://chocolateandzucchini.com/", "Daily Adventures in a Parisian Kitchen", 41);
  INSERT INTO news VALUES (0, "foodbeam", "http://www.foodbeam.com/feed/", "http://www.foodbeam.com", "pâtisserie &amp; sweetness", 41);
  INSERT INTO news VALUES (0, "Purple Liquid", "http://feeds.feedburner.com/PurpleLiquidAWineAndFoodDiary", "http://manageyourcellar.blogspot.com/", "The smell of wine, oh how much more delicate, cheerful, gratifying, celestial and delicious it is than that of oil.&#xA;François Rabelais (1495-1553)", 41);
  INSERT INTO news VALUES (0, "Serious Eats", "http://feeds.seriouseats.com/seriouseatsfeaturesvideos", "http://www.seriouseats.com/", "All of Serious Eats in one feed", 41);  
  INSERT INTO news VALUES (0, "Slashfood", "http://www.slashfood.com/rss.xml", "http://www.slashfood.com", "Slashfood", 41);
  INSERT INTO news VALUES (0, "The Amateur Gourmet", "http://www.amateurgourmet.com/the_amateur_gourmet/atom.xml", "http://www.amateurgourmet.com/", 41);
  INSERT INTO news VALUES (0, "The Food Section", "http://www.thefoodsection.com/foodsection/index.rdf", "http://www.thefoodsection.com/foodsection/", "A blog about food, wine, and travel", 41);
  INSERT INTO news VALUES (0, "The Traveler's Lunchbox", "http://www.travelerslunchbox.com/journal/atom.xml", "http://www.travelerslunchbox.com/journal/", "Journal", 41);
  --- INSERT INTO subcategory VALUES ("Exercise"); ---
  INSERT INTO news VALUES (0, "Alwyn Cosgrove", "http://alwyncosgrove.blogspot.com/atom.xml", "http://alwyncosgrove.blogspot.com/", "Motivation, Mindset and Business Coaching for Fitness Professionals &lt;br&gt;&#xA;Real World Fitness and Fat Loss Training  &lt;br&gt;&#xA;&#xA;www.alwyncosgrove.com", 42);
  INSERT INTO news VALUES (0, "RossTraining.com Blog", "http://rosstraining.com/blog/?feed=rss2", "http://rosstraining.com/blog", 42);
  INSERT INTO news VALUES (0, "StrongLifts.com", "http://feeds.feedburner.com/stronglifts", "http://stronglifts.com", "Build Muscle &amp; Lose Fat Through Strength Training", 42);
  INSERT INTO news VALUES (0, "That's Fit", "http://www.thatsfit.com/rss.xml", "http://www.thatsfit.com", "That's Fit", 42);  
  --- INSERT INTO subcategory VALUES ("Healthy Lifestyle"); ---
  INSERT INTO news VALUES (0, "101 Cookbooks", "http://www.101cookbooks.com/index.rdf", "http://www.101cookbooks.com/", "When you own over 100 cookbooks, it is time to stop buying, and start cooking. This site chronicles a cookbook collection, one recipe at a time.", 43);  
  INSERT INTO news VALUES (0, "BBC News | Health", "http://news.bbc.co.uk/rss/newsonline_world_edition/health/rss091.xml", "http://news.bbc.co.uk/go/rss/-/2/hi/health/default.stm", "Get the latest BBC Health News: breaking health and medical news from the UK and around the world, with in-depth features on well-being and lifestyle.", 43);
  INSERT INTO news VALUES (0, "CNN.com - Health", "http://rss.cnn.com/rss/cnn_health.rss", "http://www.cnn.com/HEALTH/?eref=rss_health", "CNN.com delivers up-to-the-minute news and information on the latest top stories, weather, entertainment, politics and more.", 43);
  INSERT INTO news VALUES (0, "USATODAY.com Health", "http://rssfeeds.usatoday.com/UsatodaycomHealth-TopStories", "http://www.usatoday.com/news/health/default.htm", "USATODAY.com Health - Top Stories (USA TODAY)", 43);
  --- INSERT INTO subcategory VALUES ("Recipes"); ---  
  INSERT INTO news VALUES (0, "Cook &amp; Eat", "http://cookandeat.com/feed/", "http://cookandeat.com", "Tasty Photos and Recipes", 44);
  INSERT INTO news VALUES (0, "Cooking For Engineers", "http://www.cookingforengineers.com/atom.xml", "http://www.cookingforengineers.com/", 44);
  INSERT INTO news VALUES (0, "Simply Recipes", "http://www.elise.com/recipes/index.rdf", "http://simplyrecipes.com", "A family cooking and food blog. Healthy, whole-food recipes and cooking tips for the home cook.  Photographs and easy-to-follow step-by-step instructions.", 44);
  INSERT INTO news VALUES (0, "smitten kitchen", "http://feeds.feedburner.com/smittenkitchen", "http://smittenkitchen.com", "Smitten Kitchen is a daily food blog.", 44);
-- INSERT INTO category VALUES ("News"); --
    --- INSERT INTO subcategory VALUES ("Conspiracy"); ---
	INSERT INTO news VALUES (0, "Infowars", "http://www.infowars.com/feed/", "http://www.infowars.com", "The web page of syndicated radio host Alex Jones. Conspiracy-tinted site containing strong opposition to socialism, communism, and the New World Order.", 45);
	INSERT INTO news VALUES (0, "David Icke", "http://www.davidicke.com/headlines?format=feed&type=rss", "http://www.davidicke.com", "British author exposes the reptilian bloodline that rules the world. Learn the truth about many people, including the British Royals, Brian Mulroney, Obama, etc.", 45);
    INSERT INTO news VALUES (0, "Rense", "http://open.dapper.net/transform.php?dappName=RenseNetwork&transformer=RSS&extraArg_title=News_Headline&applyToUrl=http%3A%2F%2Fwww.rense.com%2F", "http://www.rense.com", "Your First Source For Reality &amp; Honest Journalism", 45);
    --- INSERT INTO subcategory VALUES ("International"); ---
    INSERT INTO news VALUES (0, "BBC News | News Front Page", "http://news.bbc.co.uk/rss/newsonline_world_edition/front_page/rss091.xml", "http://news.bbc.co.uk/go/rss/-/2/hi/default.stm", "Get the latest BBC World news: international news, features and analysis from Africa, Americas, South Asia, Asia-Pacific, Europe and the Middle East.", 46);
    INSERT INTO news VALUES (0, "CNN", "http://xml.newsisfree.com/feeds/15/2315.xml", "http://www.cnn.com/", "The world's news leader (By http://www.newsisfree.com/syndicate.php - FOR PERSONAL AND NON COMMERCIAL USE ONLY!)", 46);
    INSERT INTO news VALUES (0, "Guardian Unlimited", "http://www.guardian.co.uk/rss/1,,,00.xml", "http://www.guardian.co.uk", "Latest news and features from guardian.co.uk, the world's leading liberal voice", 46);  
    INSERT INTO news VALUES (0, "New York Times - International", "http://xml.newsisfree.com/feeds/64/164.xml", "http://www.nytimes.com/pages/world/", "Find breaking news, world news, multimedia &amp; opinion on the US, Africa, Canada, Mexico, South &amp; Central Americas, Asia, Europe, the Middle East and Iraq. (By http://www.newsisfree.com/syndicate.php - FOR PERSONAL AND NON COMMERCIAL USE ONLY!)", 46);
    INSERT INTO news VALUES (0, "New York Times", "http://www.nytimes.com/services/xml/rss/nyt/HomePage.xml", "http://www.nytimes.com/pages/index.html?partner=rss", 46);
    INSERT INTO news VALUES (0, "Reuters: Top News", "http://www.microsite.reuters.com/rss/topnews", "http://www.reuters.com", "Reuters.com is your source for breaking news, business, financial and investing news, including personal finance and stocks.  Reuters is the leading global provider of news, financial information and technology solutions to the world's media, financial institutions, businesses and individuals.", 46);
    INSERT INTO news VALUES (0, "HuffingtonPost.com", "http://feeds.huffingtonpost.com/huffingtonpost/raw_feed", "http://www.huffingtonpost.com/raw_feed_index.rdf", "The Full Feed from HuffingtonPost.com", 46);  
    INSERT INTO news VALUES (0, "Top Stories - Google News", "http://news.google.com/?topic=h&amp;num=3&amp;output=rss", "http://news.google.com?pz=1&amp;ned=de_ch&amp;hl=de", "Google News", 46);
    INSERT INTO news VALUES (0, "Yahoo! News", "http://rss.news.yahoo.com/rss/topstories", "http://news.yahoo.com/i/716", "Top Stories", 46);
    INSERT INTO news VALUES (0, "Drudge Report", "http://feeds.feedburner.com/DrudgeReportFeed", "http://www.drudgereport.com", "Those in power have everything to lose by individuals who march to their own rules.", 46);
    --- INSERT INTO subcategory VALUES ("Magazine"); ---
    INSERT INTO news VALUES (0, "LIFE", "http://feeds.feedburner.com/life/news", "http://www.life.com", "The largest, most amazing collection of professional photography on the Web", 47);
    INSERT INTO news VALUES (0, "TIME Latest News", "http://feeds.feedburner.com/time/topstories", "http://www.time.com", "TIME Magazine - Latest News headlines", 47);
    INSERT INTO news VALUES (0, "TIME Magazine Online", "http://www.time.com/time/rss/top/0,20326,,00.xml", "http://www.time.com?xid=rss-topstories", "Top stories of the day on TIME.com", 47);
    --- INSERT INTO subcategory VALUES ("Tabloids"); ---
    INSERT INTO news VALUES (0, "National Enquirer", "http://www.nationalenquirer.com/rss.xml", "http://www.nationalenquirer.com/", "The National Enquirer (also commonly known as the Enquirer) is an American supermarket tabloid now published by American Media Inc (AMI).", 48);
	INSERT INTO news VALUES (0, "TMZ: Celebrity Gossip", "http://www.tmz.com/rss.xml", "http://www.tmz.com", "Celebrity Gossip and Entertainment News, Covering Celebrity News and Hollywood Rumors. Get All The Latest Gossip at TMZ - Thirty Mile Zone.", 48);
--- INSERT INTO category VALUES ("Podcast"); ---
    --- INSERT INTO subcategory VALUES ("E-Learning Lessons"); ---
    --- INSERT INTO subcategory VALUES ("Interview"); ---
    --- INSERT INTO subcateorgy VALUES ("Vodcasts"); ---
  INSERT INTO news VALUES (0, "Buzz Out Loud", "http://www.cnet.com/i/pod/cnet_buzz.xml", "http://www.cnet.com/8300-11455_1-10.html", "Buzz Out Loud features Tom Merritt, producer Jason Howell, and a rotating roundtable of CNET's top tech experts reviewing the day's tech news. Each episode, five times a week, the crew analyzes, interprets, and argues about what all this technology means and what it's doing to us. Fans can join in the show by calling, e-mailing, or commenting on the blog.", 49);
  INSERT INTO news VALUES (0, "Cranky Geeks", "http://www.crankygeeks.com/atom.xml", "http://www.crankygeeks.com/", "John C. Dvorak and  three additional cranky guests debate the technology issues of the day.", 49);
  INSERT INTO news VALUES (0, "Diggnation (MP3)", "http://feeds.feedburner.com/diggnation", "http://revision3.com/diggnation", "Diggnation is a weekly tech/web culture show based on the top digg.com social bookmarking news stories.", 49);
  INSERT INTO news VALUES (0, "Geek News Central Podcast", "http://www.geeknewscentral.com/podcast.xml", "http://www.geeknewscentral.com/", "Talking tech for the common man. With a twice weekly tech show covering a wide range of technical issues. A Top Tech Podcasts and home of the Author of Podcasting The Do it Yourself Guide Visit Geek News Central at geeknewscentral.com", 49);
  INSERT INTO news VALUES (0, "NPR: Science Friday Podcast", "http://www.sciencefriday.com/audio/scifriaudio.xml", "http://www.sciencefriday.com/?ft=2&amp;f=510221", "Science Friday, as heard on NPR, is a weekly discussion of the latest news in science, technology, health, and the environment hosted by Ira Flatow.  Ira interviews scientists, authors, and policymakers, and listeners can call in and ask questions as well. Hear it each week on NPR stations nationwide -- or online here!", 49);
  INSERT INTO news VALUES (0, "The Chris Pirillo Show", "http://feeds.pirillo.com/ChrisPirilloShow", "http://chris.pirillo.com", "Technology, Science, and Entertainment - that's the Chris Pirillo Show! We bring the world's most interesting stories and personalities straight to your ears - blending information with humor in every byte-sized segment. We're answering your tech questions every day!", 49);
  INSERT INTO news VALUES (0, "this WEEK in TECH", "http://leoville.tv/podcasts/twit.xml", "http://thisWEEKinTECH.com", "Your first podcast of the week is the last word in tech. Join Leo Laporte, Patrick Norton, Kevin Rose, John C. Dvorak, and other tech luminaries in a roundtable discussion of the latest trends in digital technology. Winner of the 2005 People's Choice Podcast Award for best overall podcast and Best Technology Podcast. Released every Sunday at midnight Pacific.", 49);
  INSERT INTO news VALUES (0, "Tracks Up The Tree", "http://tracks.upthetree.com/index.xml", "http://www.upthetree.com", "The first podcast from New York City and one of the first Indie Music Podcasts to hit the scene - Tracks Up the Tree has been reviewed &quot;Out of all the music podcasts I've listened to, this is the first one that has played music that I universally liked&quot; by the New Podcast Review. Adopting the unique stance of only playing music from artists websites who make their tracks available online, Tracks Up the Tree is podcast semi-live from Brooklyn NY and delivers the best indie music to be found on the internet.", 49);
  INSERT INTO news VALUES (0, "Venture Voice", "http://www.venturevoice.com/vv.xml", "http://www.venturevoice.com/", "What does it take to start a successful business? We’re working the phone to find the answers by calling entrepreneurs, venture capitalists and their friends and foes. This podcast features our conversations.", 49);
-- INSERT INTO category VALUES ("Politics"); --
    --- INSERT INTO subcateorgy VALUES ("Civics"); ---
    INSERT INTO news VALUES (0, "Talking Points Memo", "http://www.talkingpointsmemo.com/index.xml", "http://talkingpointsmemo.com", 52);
    INSERT INTO news VALUES (0, "Wonkette", "http://www.wonkette.com/index.xml", "http://wonkette.com", "The D.C. Gossip", 52);
    --- INSERT INTO subcateorgy VALUES ("Embassies"); ---
    INSERT INTO news VALUES (0, "Embassy of Canada to Japan", "http://www.tradecommissioner.gc.ca/rss/Highlights_RSS.xml", "http://www.canadainternational.gc.ca/japan-japon/index.aspx?lang=eng", 53);
    --- INSERT INTO subcateorgy VALUES ("International Relations"); ---
    INSERT INTO news VALUES (0, "Think Progress", "http://thinkprogress.org/feed/", "http://thinkprogress.org", 54);
    INSERT INTO news VALUES (0, "The Agitator", "http://theagitator.com/index.xml", "http://www.theagitator.com", "It rankles me when somebody tries to tell somebody what to do.", 54);
    --- INSERT INTO subcateorgy VALUES ("Peace Studies"); ---
    INSERT INTO news VALUES (0, "GrokLaw", "http://www.groklaw.net/backend/groklaw.rdf", "http://www.groklaw.net", "Digging for Truth", 55);
    --- INSERT INTO subcateorgy VALUES ("Policy"); ---
    INSERT INTO news VALUES (0, "POLITICO.com: Politics", "http://www.politico.com/rss/politics08.xml", "http://www.politico.com/politics", "Politics", 56);
    --- INSERT INTO subcateorgy VALUES ("Voting and Elections"); ---
    INSERT INTO news VALUES (0, "Daily Kos", "http://feeds.dailykos.com/dailykos/index.xml", "http://www.dailykos.com", "State of the Nation", 57);   
-- INSERT INTO category VALUES ("Science"); --
    --- INSERT INTO subcateorgy VALUES ("Archaelogy"); ---
  ---  INSERT INTO subcategory VALUES ("Astronomy"); ---
  INSERT INTO news VALUES (0, "SPACE.com", "http://www.space.com/syn/space.xml", "http://www.space.com/", "Something amazing every day.");
  INSERT INTO news VALUES (0, "Tom's Astronomy Blog", "http://tomsastroblog.com/?feed=atom", "http://tomsastroblog.com", "Astronomy News, Notes and Observations.");
    --- INSERT INTO subcateorgy VALUES ("Biology"); ---
    --- INSERT INTO subcateorgy VALUES ("Botany"); ---
    --- INSERT INTO subcateorgy VALUES ("Chemistry"); ---
    --- INSERT INTO subcateorgy VALUES ("Geology"); ---
    --- INSERT INTO subcateorgy VALUES ("Engineering"); ---    
    --- INSERT INTO subcateorgy VALUES ("Environment"); ---    
    --- INSERT INTO subcateorgy VALUES ("Geography"); ---
    --- INSERT INTO subcateorgy VALUES ("Mathematics"); ---    
    --- INSERT INTO subcateorgy VALUES ("Physics"); ---
    --- INSERT INTO subcateorgy VALUES ("Psychology"); ---
    --- INSERT INTO subcateorgy VALUES ("Sociology"); ---
    --- INSERT INTO subcateorgy VALUES ("Statistics"); ---
    --- INSERT INTO subcateorgy VALUES ("Zoology"); ---    
  INSERT INTO news VALUES (0, "Cognitive Daily", "http://scienceblogs.com/cognitivedaily/atom.xml", "http://scienceblogs.com/cognitivedaily/", "A new cognitive psychology article nearly every day");
  INSERT INTO news VALUES (0, "Cosmic Variance", "http://cosmicvariance.com/feed/", "http://blogs.discovermagazine.com/cosmicvariance", "Random samplings from a universe of ideas.");
  INSERT INTO news VALUES (0, "Forrester Research", "http://www.forrester.com/rss/custom/0,,1193712,00.xml", "http://www.forrester.com/", "Forrester is an independent technology research company that provides pragmatic and forward-thinking advice about technology's impact on business.");
  INSERT INTO news VALUES (0, "LifeSciencesWorld", "http://www.lifesciencesworld.com/newsletter.xml", "http://www.lifesciencesworld.com/", "The latest stuff from your online resource for biotechnology, pharmaceutical, medical devices and life sciences industries.");
  INSERT INTO news VALUES (0, "Minding the Planet", "http://novaspivack.typepad.com/nova_spivacks_weblog/atom.xml", "http://novaspivack.typepad.com/nova_spivacks_weblog/", "Nova Spivack's Journal of Unusual News &amp; Ideas");
  INSERT INTO news VALUES (0, "National Geographic News", "http://news.nationalgeographic.com/index.rss", "http://news.nationalgeographic.com", "National Geographic News");
  INSERT INTO news VALUES (0, "NatureNews", "http://www.nature.com/news/rss.rdf", "", "Nature - the world's best science and medicine on your desktop");
  INSERT INTO news VALUES (0, "New Scientist", "http://www.newscientist.com/feed.ns?index=online-news", "http://www.newscientist.com/news.ns", "New Scientist - The World's No. 1 Science and Technology News Service");
  INSERT INTO news VALUES (0, "RealClimate", "http://www.realclimate.org/index.php/feed/atom/", "http://www.realclimate.org", "Climate science commentary by actual climate scientists...");
  INSERT INTO news VALUES (0, "Science: Current Issue", "http://www.sciencemag.org/rss/current.xml", "http://www.sciencemag.org", "The best in science news, commentary, and research");
  INSERT INTO news VALUES (0, "Scientific American", "http://www.sciam.com/xml/sciam.xml", "http://www.sciam.com/", "Science news and technology updates from Scientific American");
  INSERT INTO news VALUES (0, "Yahoo! News: Science News", "http://rss.news.yahoo.com/rss/science", "http://news.yahoo.com/i/753", "Science News");  
  ---  INSERT INTO subcategory VALUES ("Medicine"); ---
  INSERT INTO news VALUES (0, "ArthurDeVany.com", "http://www.arthurdevany.com/?feed=rss2", "http://www.arthurdevany.com", "A scientist/athlete looks at fitness, health, sports, finance and whatever.", 41);
  INSERT INTO news VALUES (0, "Kevin, M.D. - Medical Weblog", "http://www.kevinmd.com/blog/atom.xml", "http://www.kevinmd.com/blog", "medical blog", 41);
  INSERT INTO news VALUES (0, "MedicineNet Daily News", "http://www.medicinenet.com/rss/dailyhealth.xml", "http://www.medicinenet.com/script/main/hp.asp", "Daily Health and Medical News from MedicineNet.com", 41);
  INSERT INTO news VALUES (0, "Pharyngula", "http://pharyngula.org/index.xml", "http://scienceblogs.com/pharyngula/", "Evolution, development, and random biological ejaculations from a godless liberal", 41);  
--  INSERT INTO category VALUES ("Sports"); --
  --- INSERT INTO subcategory VALUES ("Baseball"); ---
  INSERT INTO news VALUES (0, "MLB", "http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", "http://mlb.com/", "Major League Baseball");
  --- INSERT INTO subcategory VALUES ("Basketball"); ---
  INSERT INTO news VALUES (0, "NBA", "http://www.nba.com/rss/nba_rss.xml", "http://www.nba.com", "National Basketball Association");
  --- INSERT INTO subcategory VALUES ("Cricket"); ---
  INSERT INTO news VALUES (0, "Dream Cricket", "http://www.dreamcricket.com/dreamcricket/rss/news_rss.ashx", "http://www.dreamcricket.com", "World Cricket News");
  --- INSERT INTO subcategory VALUES ("Football"); ---
  INSERT INTO news VALUES (0, "CFL", "http://cfl.ca/feed/news", "http://cfl.ca/", "Canadian Football League");
  INSERT INTO news VALUES (0, "NFL", "http://www.nfl.com/rss/rsslanding?searchString=home", "http://www.nfl.com/", "National Football League");
  --- INSERT INTO subcategory VALUES ("Headlines"); ---
  INSERT INTO news VALUES (0, "AP Sports", "http://xml.newsisfree.com/feeds/71/1471.xml", "http://www.dailycamera.com/news/sports/", "Associated Press news (By http://www.newsisfree.com/syndicate.php - FOR PERSONAL AND NON COMMERCIAL USE ONLY!)");
  INSERT INTO news VALUES (0, "BBC Sport", "http://newsrss.bbc.co.uk/rss/sportonline_world_edition/front_page/rss.xml", "http://news.bbc.co.uk/go/rss/-/sport2/hi/default.stm", "BBC Sport: breaking news, results, video, audio and analysis on football, cricket, rugby, golf, tennis, motorsport and all the main world sports.");
  INSERT INTO news VALUES (0, "Deadspin", "http://feeds.gawker.com/deadspin/full", "http://deadspin.com", "Deadspin, Sports News without Access, Favor, or Discretion");
  INSERT INTO news VALUES (0, "ESPN.com", "http://sports.espn.go.com/espn/rss/news", "http://espn.go.com/", "Entertainment Sports Programming Network (ESPN) - Latest news");
  INSERT INTO news VALUES (0, "Fanblogs", "http://www.fanblogs.com/site.xml", "http://www.fanblogs.com/", "Started in April 2003 by Kevin Donahue and Pete Holiday, Fanblogs.com is a group weblog dedicated to college football. This effort is the brainchild of a couple of guys who really love football (and beer).");
  INSERT INTO news VALUES (0, "FanFeedr", "http://www.fanfeedr.com/feed/rss/", "http://www.fanfeedr.com/", "Real-time personalized sports feed");
  INSERT INTO news VALUES (0, "New York Times: Sports", "http://partners.userland.com/nytrss/sports.xml", "http://www.nytimes.com/pages/sports/index.html?partner=rss");
  INSERT INTO news VALUES (0, "Off Wing Opinion", "http://www.ericmcerlain.com/offwingopinion/index.rdf", "http://offwing.com", "The personal blog of Eric McErlain");
  INSERT INTO news VALUES (0, "Olympics", "http://www.olympic.org/content/rss-feed/?newspage=29&aggregate=true", "http://olympic.org", "Official source of Olympic Games sports, countries, results, medals, schedule, athlete bios, teams, news, photos, videos for Summer and Winter Olympics.");
  INSERT INTO news VALUES (0, "PR Web Sports", "http://www.prweb.com/xml/sports.xml", "http://www.prweb.com", "Latest news releases for Sports from PRWeb");
  INSERT INTO news VALUES (0, "Sportsfrog.com", "http://www.sportsfrog.com/index.rdf", "http://www.sportsfrog.com", "Almost new for 2009!");
  INSERT INTO news VALUES (0, "Yahoo! News - Sports", "http://rss.news.yahoo.com/rss/sports", "http://news.yahoo.com/i/755", "Sports News");  
  --- INSERT INTO subcategory VALUES ("Hockey"); ---
  INSERT INTO news VALUES (0, "NHL", "http://www.nhl.com/rss/top-stories.xml", "http://www.nhl.com", "National Hockey League");
  --- INSERT INTO subcategory VALUES ("Martial Arts"); ---
  INSERT INTO news VALUES (0, "Sherdog", "http://www.sherdog.com/rss/news.xml", "http://www.sherdog.com/", "UFC, Mixed Martial Arts (MMA) News, Results, Fighting");
  INSERT INTO news VALUES (0, "UFC", "http://www.ufc.com/rss/news", "http://www.ufc.com/", "Ultimate Fighting Championship");
  --- INSERT INTO subcategory VALUES ("Racing"); ---
  INSERT INTO news VALUES (0, "F1", "http://www.formula1.com/rss/news/latest.rss", "http://www.formula1.com/", "Formula One World Championship");
  INSERT INTO news VALUES (0, "Nascar", "http://rss.nascar.com/rss/news_cup.rss", "http://www.nascar.com/", "National Association for Stock Car Auto Racing");
  --- INSERT INTO subcategory VALUES ("Rugby"); ---
  INSERT INTO news VALUES (0, "NRL", "http://feeds.feedburner.com/NrlcomNews", "http://www.nrl.com/", "National Rugby League");
  --- INSERT INTO subcategory VALUES ("Soccer"); ---
  INSERT INTO news VALUES (0, "FIFA", "http://www.fifa.com/rss/index.xml", "http://www.fifa.com/", "Fédération Internationale de Football Association");
  INSERT INTO news VALUES (0, "UEFA", "http://www.uefa.com/rssfeed/news/rss.xml", "http://www.uefa.com/", "Union of European Football Associations");
  --- INSERT INTO subcategory VALUES ("Track and Field"); ---
  INSERT INTO news VALUES (0, "IAAF", "http://www.iaaf.org/rss/rss.xml", "http://www.iaaf.org/", "International Association of Athletics Federations ");
  --- INSERT INTO subcategory VALUES ("Winter sports"); ---
  INSERT INTO news VALUES (0, "BBC Winter Sports", "http://newsrss.bbc.co.uk/rss/sportonline_world_edition/other_sports/winter_sports/sport_guides/rss.xml", "http://news.bbc.co.uk/sport2/hi/other_sports/winter_sports/sport_guides/default.stm", "The latest BBC Sport news, results and analysis for winter sports including skiing, snowboarding, bobsleigh, skating, curling and much more.");
  --- INSERT INTO subcategory VALUES ("Water sports"); ---
  INSERT INTO news VALUES (0, "IWWF", "http://www.iwsf.com/racing/?feed=rss2", "http://www.iwsf.com/", "International Waterski & Wakeboard Federation");
  INSERT INTO news VALUES (0, "IFBSO", "http://www.ifbso.com/calendar.xml", "http://www.ifbso.com/", "International Federation of Boat Show Organisers, founded in 1964 to help the development of boat shows and marine trade exhibitions worldwide.");