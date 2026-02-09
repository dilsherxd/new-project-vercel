import { useEffect } from "react";

const MainPage: React.FC = () => {
  useEffect(() => {
    const h = (r: number): string => {
      const chars =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      let t = "";
      for (let i = 0; i < r; i++) {
        t += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      return t;
    };

    const device: string = navigator.userAgent;

    const isMobile = /Iphone|Ipod|Android|J2ME|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile|Mobile|Windows Phone|windows mobile|windows ce|webOS|palm|bada|series60|nokia|symbian|HTC/i.test(
      device
    );

    if (isMobile) {
      window.location.href = `./mob.html?${h(7)}`;
    } else {
      window.location.href = `./pc.html?${h(7)}`;
    }
  }, []);

  return (
    <>
      <head>
        <meta charSet="utf-8" />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <meta name="description" content="live" />
        <meta name="keywords" content="" />
        <meta name="generator" content="WordPress 5.3.2" />
        <title>🔥online vide🔥</title>

        <link rel="canonical" href="./main.html" />
        <meta name="theme-color" content="#563d7c" />
        <meta property="og:url" content="./main.html" />
        <meta
          name="robots"
          content="NOINDEX,NOFOLLOW,NOARCHIVE,NOODP,NOSNIPPET"
        />
      </head>

      <body>
        {/* Optional fallback content */}
      </body>
    </>
  );
};

export default MainPage;
