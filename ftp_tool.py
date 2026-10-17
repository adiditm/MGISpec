#!/usr/bin/env python3
"""
MGISpec FTP Management & Deployment Tool (Multi-Profile: dev / admin)
"""

import sys
import os
import json
import ftplib
from pathlib import Path

CONFIG_PATH = Path(__file__).parent / ".ftp_credentials.json"

def get_credentials(profile=None):
    if not CONFIG_PATH.exists():
        print(f"Error: Credentials file not found at {CONFIG_PATH}")
        sys.exit(1)
    with open(CONFIG_PATH, "r") as f:
        data = json.load(f)
    
    if "profiles" in data:
        p_name = profile or data.get("default_profile", "dev")
        if p_name not in data["profiles"]:
            print(f"Error: Profile '{p_name}' not found. Available: {list(data['profiles'].keys())}")
            sys.exit(1)
        creds = data["profiles"][p_name]
        creds["_profile_name"] = p_name
        return creds
    else:
        return data

def get_connection(profile=None):
    creds = get_credentials(profile)
    ftp = ftplib.FTP(timeout=15)
    ftp.connect(creds["host"], creds.get("port", 21))
    ftp.login(creds["user"], creds["pass"])
    return ftp, creds

def cmd_test(profile=None):
    creds = get_credentials(profile)
    p_name = creds.get("_profile_name", "default")
    print(f"Testing FTP connection for profile [{p_name}] ({creds['user']} @ {creds['host']})...")
    try:
        ftp, _ = get_connection(profile)
        print("Connected successfully!")
        print(f"Current Working Directory: {ftp.pwd()}")
        print("\nDirectory Listing:")
        ftp.dir()
        ftp.quit()
        return True
    except Exception as e:
        print(f"Connection failed: {e}")
        return False

def cmd_ls(remote_path="", profile=None):
    ftp, creds = get_connection(profile)
    p_name = creds.get("_profile_name", "default")
    print(f"[{p_name}] Directory listing of: {ftp.pwd() if not remote_path else remote_path}")
    if remote_path:
        ftp.cwd(remote_path)
    ftp.dir()
    ftp.quit()

def cmd_upload(local_path, remote_path=None, profile=None):
    local_p = Path(local_path)
    if not local_p.exists():
        print(f"Error: Local file '{local_path}' does not exist.")
        return False
    
    if remote_path is None:
        remote_path = local_p.name
    
    ftp, creds = get_connection(profile)
    remote_dir = os.path.dirname(remote_path)
    file_name = os.path.basename(remote_path)
    if remote_dir:
        ensure_remote_dir(ftp, remote_dir)
        
    print(f"[{creds.get('_profile_name')}] Uploading '{local_path}' -> '{remote_path}'...")
    with open(local_p, "rb") as f:
        ftp.storbinary(f"STOR {file_name}", f)
    print("Upload completed successfully.")
    ftp.quit()
    return True

def cmd_download(remote_path, local_path=None, profile=None):
    if local_path is None:
        local_path = Path(remote_path).name
    
    ftp, creds = get_connection(profile)
    print(f"[{creds.get('_profile_name')}] Downloading '{remote_path}' -> '{local_path}'...")
    with open(local_path, "wb") as f:
        ftp.retrbinary(f"RETR {remote_path}", f.write)
    print("Download completed successfully.")
    ftp.quit()
    return True

def cmd_cat(remote_path, profile=None):
    ftp, _ = get_connection(profile)
    lines = []
    ftp.retrlines(f"RETR {remote_path}", lines.append)
    ftp.quit()
    print("\n".join(lines))

def cmd_rm(remote_path, profile=None):
    ftp, creds = get_connection(profile)
    print(f"[{creds.get('_profile_name')}] Deleting '{remote_path}'...")
    try:
        ftp.delete(remote_path)
        print("File deleted successfully.")
    except Exception as e:
        print(f"Failed to delete: {e}")
    ftp.quit()


def ensure_remote_dir(ftp, remote_dir):
    parts = [p for p in remote_dir.strip("/").split("/") if p]
    current = ""
    for part in parts:
        current += "/" + part
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            try:
                ftp.mkd(current)
                ftp.cwd(current)
            except Exception:
                pass

def cmd_upload_dir(local_dir, remote_base="", profile=None):
    local_path = Path(local_dir)
    if not local_path.is_dir():
        print(f"Error: '{local_dir}' is not a directory.")
        return False
    
    ftp, creds = get_connection(profile)
    root_pwd = ftp.pwd()
    print(f"[{creds.get('_profile_name')}] Uploading directory '{local_dir}' to '{remote_base or root_pwd}'...")
    
    for root, dirs, files in os.walk(local_path):
        rel_path = os.path.relpath(root, local_path)
        if rel_path == ".":
            target_remote = remote_base.strip("/")
        else:
            target_remote = (remote_base.strip("/") + "/" + rel_path).strip("/")
        
        if target_remote:
            ensure_remote_dir(ftp, target_remote)
            ftp.cwd(root_pwd + ("/" + target_remote if target_remote else ""))
        else:
            ftp.cwd(root_pwd)
        
        for file in files:
            if file.startswith("."):
                continue
            local_file = os.path.join(root, file)
            print(f"Uploading {local_file} -> {target_remote}/{file}...")
            with open(local_file, "rb") as f:
                ftp.storbinary(f"STOR {file}", f)
                
    ftp.quit()
    print("\nDirectory upload completed successfully.")
    return True

def main():
    args = sys.argv[1:]
    profile = None
    
    # Check for --profile flag
    if "--profile" in args:
        idx = args.index("--profile")
        if idx + 1 < len(args):
            profile = args[idx + 1]
            del args[idx:idx + 2]
            
    if not args:
        print("Usage: ftp_tool.py [--profile dev|admin] [test|ls|upload|download|upload_dir|cat] [args...]")
        sys.exit(1)
        
    cmd = args[0]
    if cmd == "test":
        cmd_test(profile)
    elif cmd == "ls":
        cmd_ls(args[1] if len(args) > 1 else "", profile)
    elif cmd == "upload":
        if len(args) < 2:
            print("Usage: ftp_tool.py upload <local_file> [remote_name]")
            sys.exit(1)
        cmd_upload(args[1], args[2] if len(args) > 2 else None, profile)
    elif cmd == "download":
        if len(args) < 2:
            print("Usage: ftp_tool.py download <remote_file> [local_name]")
            sys.exit(1)
        cmd_download(args[1], args[2] if len(args) > 2 else None, profile)
    elif cmd == "upload_dir":
        if len(args) < 2:
            print("Usage: ftp_tool.py upload_dir <local_dir> [remote_dir]")
            sys.exit(1)
        cmd_upload_dir(args[1], args[2] if len(args) > 2 else "", profile)
    elif cmd == "cat":
        if len(args) < 2:
            print("Usage: ftp_tool.py cat <remote_file>")
            sys.exit(1)
        cmd_cat(args[1], profile)
    elif cmd == "rm":
        if len(args) < 2:
            print("Usage: ftp_tool.py rm <remote_file>")
            sys.exit(1)
        cmd_rm(args[1], profile)
    else:
        print(f"Unknown command: {cmd}")

if __name__ == "__main__":
    main()
